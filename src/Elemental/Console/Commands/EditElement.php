<?php namespace Elemental\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Element;

class EditElement extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'element:edit';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Edit the attributes of an element.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$slug = $this->argument('slug');
		$newNickname =  trim($this->option('nickname'));
		$newSlug =  trim($this->option('slug'));
		
		$inputAttributes = $this->option('attribute');

		if(!strlen($newNickname) && !strlen($newSlug) && empty($inputAttributes)) {
			$this->error('No input provided');
			exit;
		}


		if(!empty($inputAttributes)) {
			$attributes = array();
			foreach($inputAttributes as $attr) {
				$arr = explode(':', $attr);
				$attributes[str_replace('=', '', $arr[0])] = $arr[1];
			}

			if(Element::update($slug, $attributes)){
				$this->info('Element attributes updated');
			} else {
				$this->displayErrors(Element::errors());
			}
		}

		if(strlen($newNickname) || strlen($newSlug)) {
			$updates = [];
			if(strlen($newNickname)) {
				$updates['nickname'] = $newNickname;
				if(!strlen($newSlug)) {
					$createSlug = $this->confirm('You are assigning a new nickname but not a new slug. Do you me to create a new slug from the new nickname? [yes|no]');
					if($createSlug) {
						$updates['slug'] = strtolower(str_replace(' ', '_', $newNickname));
					}
				}
			}

			if(strlen($newSlug)) {
				$updates['slug'] = $newSlug;
			}

			if(Element::updateMeta($slug, $updates)) {
				$this->info('Element metadata updated');
			} else {
				$this->displayErrors(Element::errors());	
			}
		}
		

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			['slug', InputArgument::REQUIRED, 'The slug of the element you want to edit.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			['attribute', 'a', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Attributes to modify, expressed as --attribute "key:value"', null],
			['nickname', 'k', InputOption::VALUE_OPTIONAL, 'Updates the element nickname.'],
			['slug', 's', InputOption::VALUE_OPTIONAL, 'Updates the element slug.'],
		];
	}


	protected function displayErrors($errors)
	{
		$errorList = '';
		foreach ($errors[0] as $key => $msg) {
			$errorList .= $msg[0]."\n";	
		}
		$this->error("Error: \n".$errorList);
	}

}
