<?php

namespace Tests;

use Laravel\Dusk\Browser as BaseBrowser;

class Browser extends BaseBrowser {
	public function waitUntilMissingModal(): self {
		return $this->waitUntilMissing('@modal-wrapper');
	}

	/**
	 * Click an element via JavaScript rather than a native WebDriver click, so the
	 * click isn't blocked when a fixed-position dev tool (e.g. Laravel Debugbar's
	 * resize handle) overlaps the element's on-screen position.
	 */
	public function clickViaJs(string $selector): self {
		$cssSelector = $this->resolver->format($selector);

		$this->script('document.querySelector(' . json_encode($cssSelector) . ').click();');

		return $this;
	}
}