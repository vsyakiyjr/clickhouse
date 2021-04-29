<div class="cms-block cms-block-feedback">
	<a class="btn-custom-feedback" av-feedback config="{{ json_encode($contentBlock->config) }}">
		{{ $contentBlock->config['button_text'] ?? trans_sl('parts.footer.contacts.feedback.resume') }}
	</a>
</div>