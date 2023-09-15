<?php

namespace Botble\Ecommerce\Supports;

use Botble\Ecommerce\Models\Broadcast;
use Botble\Ecommerce\Models\BroadcastCustomer;
use Botble\Ecommerce\Models\BroadcastRead;
use Botble\Ecommerce\Models\Product;

class NotificationHelper
{
    public function getAllDataNotification()
    {
        $notification = [];
        $notification_total = 0;
        $count_read = 0;
        if(auth('customer')->check()){
            $get_notifications = BroadcastCustomer::where('customer_id', auth('customer')->id())
                ->with('broadcast')
                ->latest()
                ->get();

            $notif_customer = [];
            foreach ($get_notifications->take(5) as $get_notification) {
                $notif_customer[] = $get_notification->broadcast;
            }

            $get_notification_all = Broadcast::where('target', 'all')
                ->latest()
                ->get();

            $merged_notifications = collect($notif_customer)->concat($get_notification_all)->sortByDesc('created_at');

            foreach($merged_notifications as $merged_notification){
                $is_read = BroadcastRead::where([['customer_id',auth('customer')->id()],['broadcast_id',$merged_notification->id]])->first();
                if($is_read){
                    $count_read += 1;
                    $merged_notification->is_read = 1;
                }else{
                    $merged_notification->is_read = 0;
                }
                $notification_total += 1;
            }

            $merged_notifications = collect($notif_customer)->concat($get_notification_all->take(5))->sortByDesc('created_at');

            foreach($merged_notifications->take(5) as $merged_notification){
                $template = $merged_notification->description;

                $username = auth('customer')->user()->name;
                $product = Product::where('id',$merged_notification->product_id)->first();
                $limit = 20;

                // Mengganti '{{ customer_name }}' dengan $username
                $template = preg_replace('/{{\s*customer_name\s*}}/', $username, $template);

                if($product){
                    // Memotong nama produk sesuai dengan batasan karakter
                    $productLimited = '"'.substr($product->name, 0, $limit) . '..."';
                    $template = preg_replace('/{{\s*product_name\s*}}/', $productLimited, $template);
                }

                // Menghilangkan placeholder yang tidak ditemukan dalam template
                $template = preg_replace('/{{\s*[^{}]+\s*}}/', '', $template);

                $merged_notification->description = $template;

                $notification[] = $merged_notification;
            }
        }

        return (object)array(
            'notifications' => $notification,
            'count_unread' => $notification_total - $count_read
        );
    }
}
