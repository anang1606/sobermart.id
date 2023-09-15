<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Assets;
use Botble\Ecommerce\Models\Review;
use Botble\Ecommerce\Repositories\Interfaces\ReviewInterface;
use Botble\Marketplace\Tables\ReviewTable;
use Illuminate\Http\Request;
use MarketplaceHelper;

class ReviewController
{
    protected ReviewInterface $reviewRepository;

    public function __construct(ReviewInterface $reviewRepository)
    {
        $this->reviewRepository = $reviewRepository;
    }

    public function index(ReviewTable $table)
    {
        page_title()->setTitle(__('Reviews'));

        Assets::addStylesDirectly('vendor/core/plugins/ecommerce/css/review.css');

        return $table->render(MarketplaceHelper::viewPath('dashboard.table.reviews'));
    }

    public function replyReviews(Request $request){
        $review = $this->reviewRepository->getModel()
        ->where('id',$request->id_comment)
        ->first();

        if($review){
            $reply_review = new Review;
            $reply_review->parent_id = $review->id;
            $reply_review->customer_id = auth('customer')->user()->store->id;
            $reply_review->star = $review->star;
            $reply_review->status = $review->status;
            $reply_review->comment = $request->comment;
            $reply_review->save();

            if($reply_review){
                $review->is_reply = 1;
                if($review->save())
                    return redirect(route('marketplace.vendor.reviews.index'));
            }
        }
    }

    public function getReplyReviews(Request $request){
        $query = Review::where('parent_id', $request->group)->get();

        $table = '<table>';
            $table .= '<thead>
                <tr>
                    <th style="text-align:left;padding: 5px 12px !important;" width="40px">No.</th>
                    <th style="text-align:left;" width="97%">Reply Comment</th>
                </tr>
            </thead>';

        $table .= '<tbody>';
        $no = 1;

        foreach($query as $qr){
            $table .= '<tr>';
            $table .= "<td style='text-align:left;'>".$no++."</td>";
            $table .= "<td style='text-align:left;'>".$qr->comment."</td>";
            $table .= '</tr>';
        }
        $table .='</tbody>';

        $table .= '</table>';

        return $table;
    }

}
