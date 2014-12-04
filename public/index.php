<?php 
 require_once '../lib/inc.php';
 
 $liste = listFilme();
 
?><!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gute Filme</title>

    <!-- Bootstrap -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/fonts/kingthings/stylesheet.css" />
    <link rel="stylesheet" type="text/css" href="/css/jquery.jgrowl.css" />
	<link rel="stylesheet" type="text/css" href="/css/style.css" />
	
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
  
  <div id="main">
    <h1>Gute Filme 
    <button type="button" class="btn btn-default" aria-label="Left Align" data-toggle="modal" data-target="#modal-new-movie">
	  <i class="fa fa-plus"></i>
	</button>
	<button type="button" class="btn btn-default" aria-label="Left Align" data-toggle="modal" data-target="#modal-new-recip">
	  <i class="fa fa-user"></i>
	</button></h1>
	
	<p>In Wuppertal gibt es auch glückliche Menschen!</p>

	
	  <!-- Table -->
	  <table class="table table-striped" id="movie-table">
	    <thead><tr><th width="40">Cover</th><th>Name</th><th>Genre</th><th width="50">&nbsp;</th></tr></thead>
	    <tbody></tbody>
	  </table>

	  
	  
<!-- Modal Details -->
<div class="modal modal-large fade" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="movie-details" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button onclick="movie.killTrailer();" type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Schließen</span></button>
        <h4 class="modal-title" id="detail-name"></h4>
      </div>
      <div class="modal-body">
        
        <div id="detail-body">
        
        </div>
        
        <div id="detail-trailer"></div>
        
        
      </div>
      <div class="modal-footer">
        <button onclick="movie.killTrailer();" type="button" class="btn btn-default" data-dismiss="modal">Schließen</button>
        <a id="movie-trailer" class="btn btn-primary" href="#" target="_blank">Trailer</a>
      </div>
    </div>
  </div>
</div>
	  
	

<!-- Modal Neuer Recipient -->
<div class="modal fade" id="modal-new-recip" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Schließen</span></button>
        <h4 class="modal-title">Jemand zur Liste einladen</h4>
      </div>
      <div class="modal-body">
        <form role="form" id="recip-form">
		  <div class="form-group">
		    <input name="name" type="text" class="form-control" id="recip-name" placeholder="Name">
		  </div>
		  
		  <div class="form-group">
		    <input name="email" type="email" class="form-control" id="recip-email" placeholder="E-Mail Adresse">
		  </div>

		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
        <button id="recip-save" type="button" class="btn btn-primary">Speichern</button>
      </div>
    </div>
  </div>
</div>

	
<!-- Modal Neuer Film -->
<div class="modal fade" id="modal-new-movie" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title">Neuer Film</h4>
      </div>
      <div class="modal-body">
        <form role="form" id="movie-form">
          <input type="hidden" name="id" id="movie-id" value="0">
		  <div class="form-group">
		    <input name="name" type="text" class="form-control" id="movie-name" placeholder="Name des Films">
		  </div>
		  
		  <div class="form-group">
		    <input name="cover" type="text" class="form-control" id="movie-cover" placeholder="URL zum Cover">
		  </div>
		  
		  <div class="form-group">
		    <textarea name="desc" class="form-control" id="movie-desc" placeholder="Beschreibung"></textarea>
		  </div>
		  
		  <div class="form-group">
		    <input name="trailer" type="text" class="form-control" id="movie-trailer" placeholder="Link zum Trailer auf youtube">
		  </div>
		  
		  <div class="form-group">
		    <div class="checkbox">
			  <label>
			    <input type="checkbox" value="thriller" name="genre[]"> Thriller
			  </label>
			</div>
			<div class="checkbox">
			  <label>
			    <input type="checkbox" value="scifi" name="genre[]"> Science-Fiction
			  </label>
			</div>
			<div class="checkbox">
			  <label>
			    <input type="checkbox" value="horror" name="genre[]"> Grusel
			  </label>
			</div>
			<div class="checkbox">
			  <label>
			    <input type="checkbox" value="drama" name="genre[]"> Dramatisch
			  </label>
			</div>
			<div class="checkbox">
			  <label>
			    <input type="checkbox" value="action" name="genre[]"> Action
			  </label>
			</div>
			<div class="checkbox">
			  <label>
			    <input type="checkbox" value="lustig" name="genre[]"> Lustig
			  </label>
			</div>
			<div class="checkbox">
			  <label>
			    <input type="checkbox" value="doku" name="genre[]"> Doku
			  </label>
			</div>
			<div class="checkbox">
			  <label>
			    <input type="checkbox" value="schnulze" name="genre[]"> Schnulzig
			  </label>
			</div>
		  </div>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Abbrechen</button>
        <button id="movie-save" type="button" class="btn btn-primary">Speichern</button>
      </div>
    </div>
  </div>
</div>

</div>
<!-- Neuer Film Modal Ende -->
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="/js/jquery.js"></script>
    <script src="/js/tablesorter/jquery.tablesorter.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/script.js"></script>
    <script src="/js/jquery.jgrowl.min.js"></script>
  </body>
</html>