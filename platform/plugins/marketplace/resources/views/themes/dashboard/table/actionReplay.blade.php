<div class="table-actions">
    @if (!$item->is_reply)
        <a href="#" data-reviews="{{ base64_encode($item->comment) }}" data-comment="{{ base64_encode($item->id) }}"
            class="btn btn-icon btn-sm btn-primary replyDialog" data-bs-toggle="tooltip"
            data-bs-original-title="Reply Reviews"><i class="fa fa-reply"></i></a>
    @endif
</div>
