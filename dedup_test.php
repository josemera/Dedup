<?php

	Class Dedup {

		public $records = [];
		public $id_dupes = [];
		public $email_dupes = [];
		public $dupes_exist = false;

		public function __construct($filename)
		{
			if( ! file_exists($filename)) {
				die("File $filename does not exist\n\n");
			}

			$file_contents = file_get_contents($filename);
			if( ! $file_contents) {
				die("Unable to read file : $filename\n\n");
			}

			$this->records = json_decode($file_contents, true);
			if( ! $this->records) {
				die("Invalid format, file must contain json formatted text\n\n");
			}
		}

		public function run()
		{
			$this->reconcile();
			file_put_contents('results.json', json_encode($this->records));
		}

		protected function reconcile()
		{
			foreach($this->records['leads'] as $k=>$v) {
				$this->id_dupes[$v['_id']][] = $k;

				if(count($this->id_dupes[$v['_id']]) > 1) {
					$this->dupes_exist = true;
				}
			}

			if($this->dupes_exist) {
				foreach($this->id_dupes as $v) {
					$this->remove_dupes($v);	
				}
			}


			foreach($this->records['leads'] as $k=>$v) {
				$this->email_dupes[$v['email']][] = $k;

				if(count($this->id_dupes[$v['_id']]) > 1) {
					$this->dupes_exist = true;
				}
			}

			if($this->dupes_exist) {
				foreach($this->email_dupes as $v) {
					$this->remove_dupes($v);	
				}
			}
		}

		protected function remove_dupes($dupe_ids)
		{
			$dupe_ids = array_reverse($dupe_ids);  
			$latest = array_shift($dupe_ids); 

			foreach ($dupe_ids as $v) {
				if(strtotime($this->records['leads'][$v]['entryDate']) > strtotime($this->records['leads'][$latest]['entryDate'])) {
					echo "Removing record with id : {$this->records['leads'][$latest]['_id']}  and email : {$this->records['leads'][$latest]['email']} \n\n";
					unset($this->records['leads'][$latest]);
					$latest = $v;
				} else {
					echo "Removing record with id : {$this->records['leads'][$v]['_id']}  and email : {$this->records['leads'][$v]['email']} \n\n";
					unset($this->records['leads'][$v]);
				}
			}

			$this->dupes_exist = false;
		}

	}

	if( ! isset($argv[1])) {
		die("Filename with records must be specified \n\n");
	}

	$dedup = new Dedup($argv[1]);
	$dedup->run();

?>