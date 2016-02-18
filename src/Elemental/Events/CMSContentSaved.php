<?php namespace Elemental\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class CMSContentSaved extends Event {

	use SerializesModels;

	public $data;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct($data)
	{
		$this->data = $data;
	}

}
