<?php namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Osiset\ShopifyApp\Objects\Values\ShopDomain;
use stdClass;

class OrdersCancelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Shop's myshopify domain
     *
     * @var ShopDomain|string
     */
    public $shopDomain;

    /**
     * The webhook data
     *
     * @var object
     */
    public $data;

    /**
     * Create a new job instance.
     *
     * @param string   $shopDomain The shop's myshopify domain.
     * @param stdClass $data       The webhook data (JSON decoded).
     *
     * @return void
     */
    public function __construct($shopDomain, $data)
    {
        $this->shopDomain = $shopDomain;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->shopDomain = ShopDomain::fromNative($this->shopDomain);
        $shop = User::where('name',$this->shopDomain->toNative())->first();
        $orderData = $this->data;
        foreach ($orderData['line_items'] as $data) {
            if (!empty($data['properties'])) {
                foreach ($data['properties'] as $property) {
                    if (strpos($property['name'], '_inventory_packet') !== false) {
                        $variantQty = explode('_', $property['value']);
                        $variantID = explode('--', $variantQty[1]);
                        $qty = rtrim($variantQty[0], 'x');
                        $response = $shop->api()->rest('GET', '/admin/api/'.env('SHOPIFY_API_VERSION').'/variants/' . $variantID[0] . '.json');
                        $response = isset($response['body']['variant']) ? $response['body']['variant'] : null;
                        if ($response) {
                            $inventoryItemId = $response->inventory_item_id;
                            if ($inventoryItemId) {
                                $data = ['inventory_item_ids' => $inventoryItemId];
                                $inventoryLevel = $shop->api()->rest('GET', '/admin/api/'.env('SHOPIFY_API_VERSION').'/inventory_levels', $data);
                                $inventoryLevel = isset($inventoryLevel['body']['inventory_levels']) ? $inventoryLevel['body']['inventory_levels'] : null;
                                if ($inventoryLevel) {
                                    $inventoryResponse = json_decode(json_encode($inventoryLevel), 1);
                                    $inventoryObj = [];
                                    $inventoryObj['location_id'] = $inventoryResponse[0]['location_id'];
                                    $inventoryObj['inventory_item_id'] = $inventoryResponse[0]['inventory_item_id'];
                                    $inventoryObj['available_adjustment'] = $qty;
                                    $saveInventory = $shop->api()->rest('POST', '/admin/api/'.env('SHOPIFY_API_VERSION').'/inventory_levels/adjust', $inventoryObj);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
