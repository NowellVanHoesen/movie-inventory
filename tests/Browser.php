<?php

namespace Tests;

use Laravel\Dusk\Browser as BaseBrowser;

class Browser extends BaseBrowser {
	public function waitUntilMissingModal(): self {
		return $this->waitUntilMissing('@modal-wrapper');
	}
}