<?php 
class Xhr
{
	
	public function detail()
	{
		if($movie = Db::getMovie($_POST['id']))
		{
			$this->out(array(
				'movie' => $movie
			));
		}
	}
	
	public function edit()
	{
		if($movie = Db::getMovie($_POST['id']))
		{
			$this->out(array(
				'movie' => $movie
			));
		}
	}
	
	public function saverecip()
	{
		/*
		  	[name] => Peter
    		[email] => peter@pan.de
		 */
		if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
		{
			$this->out(array('status' => 0));
		}
		else
		{
			$name = strip_tags($_POST['name']);
			$email = $_POST['email'];
			
			if($id = Db::insert('recip',array(
				'name' => $name,
				'email' => $email
			)))
			{
				$this->out(array('id' => $id));
			}
		}
		$this->out(array('status' => 0));
	}
	
	public function del()
	{
		if($movie = Db::findOne('movie',array('_id' => new MongoId($_POST['id']))))
		{
			$this->delImages($movie);
			
			Db::delete('movie',$_POST['id']);
		}
		
	}
	
	private function delImages($movie)
	{
		$sizes = array(200,150);
		$crops = array(75,50,30);
			
		if(isset($movie['cover']) && !empty($movie['cover']))
		{
			@unlink('./img/cover/' . $movie['cover']);
			foreach ($sizes as $s)
			{
				@unlink('./img/cover/r-' . $s . '-' . $movie['cover']);
			}
				
			foreach ($crops as $c)
			{
				@unlink('./img/cover/c-' . $c . '-' . $movie['cover']);
			}
		}
	}
	
	public function loadmovies()
	{
		if($movies = Db::listAll('movie',array('id','name','cover','genre')))
		{
			$this->out(array(
				'movies' => $movies
			));
		}
	}
	
	public function savemovie()
	{		/*
		 [name] => sydc
		[desc] => ddfg
		[trailer] => sdf
		[genre] => Array
		(
				[0] => horror
		)
		*/
		if(isset($_POST['id']))
		{
			$name = strip_tags($_POST['name']);
			$desc = strip_tags($_POST['desc']);
			$trailer = strip_tags($_POST['trailer']);
			$cover = '';
			
			if($_POST['cover'] != '')
			{
				
				
				$oldname = explode('/',$_POST['cover']);
				$oldname = end($oldname);
					
				if(@copy($_POST['cover'],'../tmp/' . $oldname) != null)
				{
					try {
						$image = new fImage('../tmp/' . $oldname);
						
						$image->resize(250, 0);
						$image->saveChanges();
						
						$newname = uniqid() . '.' . $image->getExtension();
						copy('../tmp/' . $oldname, './img/cover/' . $newname);
						$image->delete();
						
						$sizes = array(200,150);
						$crops = array(75,50,30);
						
						foreach ($sizes as $s)
						{
							$nfile = './img/cover/r-' . $s . '-' . $newname;
							copy('./img/cover/' . $newname,$nfile);
							$image = new fImage($nfile);
							$image->resize($s, 0);
							$image->saveChanges();
						}
						
						foreach ($crops as $c)
						{
							$nfile = './img/cover/c-' . $c . '-' . $newname;
							copy('./img/cover/' . $newname,$nfile);
							$image = new fImage($nfile);
							$image->cropToRatio(1, 1);
							$image->resize($c, $c);
							$image->saveChanges();
						}
						
						$cover = $newname;
					}
					catch (Exception $e)
					{
						$cover = '';
					}
					
				}
			}
	
			$tmp = array();
			$genre = array();
			if(isset($_POST['genre']))
			{
				foreach ($_POST['genre'] as $g)
				{
					if(!isset($tmp[$g]))
					{
						$genre[] = $g;
					}
					$tmp[$g] = true;
				}
			}
			else
			{
				$this->out(array('status' => 0, 'error' => 'Du musst nen Genre aussuchen!'));
			}
	
			if($name != '')
			{
				if($_POST['id'] == '0')
				{
					if($id = Db::insert('movie',array(
							'name' => $name,
							'cover' => $cover,
							'desc' => $desc,
							'trailer' => $trailer,
							'genre' => $genre
					)))
					{
						
						if($recips = Db::listAll('recip',array('name','email')))
						{
							$email = new fEmail();
							foreach ($recips as $r)
							{
								$email->addRecipient($r['email']);
							}
							$email->setSubject('Neuer Film: ' . $name);
							
							$message = 'http://gute-filme.vahp.de'."\n\n".$name."\n\n".$desc;
							
							if(!empty($cover))
							{
								$cover = '<img style="float:left;margin:0 10px 10px 0;border-radius:6px;" src="http://gute-filme.vahp.de/img/cover/r-200-'.$cover.'" />';
							}
							
							$message_html = '<a href="http://gute-filme.vahp.de/">gute-filme.vahp.de</a>'."\n\n<h1>".$name."</h1>\n\n" . $cover . '<p>'.nl2br($desc).'</p><div style="clear:both;"></div>' . "<br /><br />-- <br />" . '<a href="http://gute-filme.vahp.de/">gute-filme.vahp.de</a>';
							
							$email->setBody($name . ' wurde ');
							$email->setHTMLBody($message_html);
							
							$email->setFromEmail(DEFAULT_EMAIL, 'Filmliste');
							
							$smtp = new fSMTP(SMTP_HOST,SMTP_PORT,SMTP_SSL);
							$smtp->authenticate(SMTP_USER, SMTP_PASS);
							
							$email->send($smtp);
							
							$smtp->close();
						}
						
						$this->out(array(
							'movie' => Db::getMovie($id)
						));
					}
				}
				else if(strlen($_POST['id']) > 6)
				{
					if($old = Db::getMovie($_POST['id']))
					{
						if($cover != '')
						{
							$this->delImages($old);
						}
						else
						{
							$cover = $old['cover'];
						}
					}
					// update($collection, $id, $doc)
					Db::update('movie', $_POST['id'], array(
						'name' => $name,
						'cover' => $cover,
						'desc' => $desc,
						'trailer' => $trailer,
						'genre' => $genre
					));
					
					$this->out(array(
							'status' => 1
					));
				}
			}
		}
		
		$this->out(array('status' => 0));
	}
	
	private function out($data)
	{
		header('Content-type: application/json');
	
		if(!isset($data['status']))
		{
			$data['status'] = 1;
		}
		echo json_encode($data);
		exit();
	}
}