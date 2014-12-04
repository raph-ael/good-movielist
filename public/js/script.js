$(document).ready(function(){
	movie.init();
});

var ajax = {
	req: function(action,opt){
		
		stopLoader();
		var data = {};
		var success = function(){};
		var error = function(){};
		
		if(opt.success != undefined)
		{
			success = opt.success;
		}
		
		if(opt.error != undefined)
		{
			success = opt.error;
		}
		
		if(opt.data != undefined)
		{
			data = opt.data;
		}
		
		$.ajax({
			url: '/xhr.php?a=' + action,
			type: 'post',
			data: data,
			success: function(ret)
			{
				success(ret);
			},
			error: function()
			{
				
			},
			complete: function()
			{
				stopLoader();
			}
		});
	}
};

var movie =
{
	$table: null,
	$detail_body: null,
	$detail_title: null,
	$detail_trailer: null,
	$detail_modal:null,
	$trailer_button:null,
	
	$movie_save:null,
	
	active:null,
	
	init: function()
	{
		this.initActions();
		this.$table = $('#movie-table tbody');
		this.$detail_modal = $('#modal-detail');
		this.$trailer_button = $('#movie-trailer');
		
		this.$detail_name = $('#detail-name');
		this.$detail_body = $('#detail-body');
		this.$detail_trailer = $('#detail-trailer');
		
		this.$movie_save = $('#movie-save');
		
		this.list();
	},
	
	killTrailer: function()
	{
		this.$detail_body.show();
		this.$detail_trailer.html('');
		this.$detail_trailer.hide();
	},
	
	initActions: function()
	{
		// youtube_parser
		
		$('#movie-trailer').click(function(ev){
			
			id = youtube_parser(movie.active.trailer);
			
			if(id !==false)
			{
				ev.preventDefault();
				var width = parseInt(movie.$detail_body.width());
				var height = parseInt(0.5625*width);
				
				if(width < 30)
				{
					width = 560;
					height = 315;
				}
				
				movie.$detail_body.hide();
				movie.$detail_trailer.html('<iframe width="'+width+'" height="'+height+'" src="//www.youtube-nocookie.com/embed/' + id + '?rel=0&amp;showinfo=0&amp;autoplay=1" frameborder="0" allowfullscreen></iframe>');
				movie.$detail_trailer.show();
			}
		});
		
		$('#recip-save').click(function(){
			
			ajax.req('saverecip',{
				data: $('#recip-form').serialize(),
				success: function(ret){					
					if(ret.status == 1)
					{
						$('#modal-new-recip').modal('hide');
						info('Eingeladen!');
						$('#recip-email').val('');
						$('#recip-name').val('');
					}
					else
					{
						error('Mit der E-Mail Adressse stimmt etwas nicht.');
					}
				}
			});
		});
		
		$('#movie-save').click(function(){
			
			
			var check = true;
			var trailer = $('#movie-trailer').val();
			
			if(trailer != '' && youtube_parser(trailer) == false)
			{
				$('#movie-trailer').val('');
				check = false;
				error('Im Momment gehen nur youtube Trailer');
				
			}
			
			if(check)
			{
				loader(movie.$movie_save);
				$.ajax({
					url: '/xhr.php?a=savemovie',
					type: 'post',
					data: $('#movie-form').serialize(),
					dataType: 'json',
					success: function(ret)
					{
						stopLoader(movie.$movie_save);
						if(ret.status == 0)
						{
							if(ret.error != undefined)
							{
								error(ret.error);
							}
							else
							{
								error('Gib nen Namen ein!');
							}
						}
						else
						{
							$('#modal-new-movie').modal('hide');
							info('Film gespeichert!');
							if(ret.movie != undefined)
							{
								movie.append(ret.movie);
							}
							
							movie.clearForm();
							
						}
					}
				});
			}
		});
	},

	del: function(id)
	{
		if(confirm('Soll der Film wirklich gelöscht werden?'))
		{
			ajax.req('del',{
				data: {id:id},
				success: function()
				{
					$('#m-' + id).remove();
				}
			});
		}
	},
	
	edit: function(id)
	{
		ajax.req('edit',{
			data: {id:id},
			success: function(ret)
			{
				movie.setEdit(ret.movie);
				$('#modal-new-movie').modal('show');
			}
		});
	},
	
	setEdit: function(movie)
	{
		$('#movie-id').val(movie.id);
		$('#movie-name').val(movie.name);
		$('#movie-cover').val('');
		$('#movie-desc').val(movie.desc);
		$('#movie-trailer').val(movie.trailer);
		
		$('#movie-form .checkbox input').each(function(){
			this.checked = false;
		});
		
		for(var i=0;i<movie.genre.length;i++)
		{
			$el = $('#movie-form .checkbox input[value='+movie.genre[i]+']');
			if($el.length > 0)
			{
				$el[0].checked = true;
			}
		}
		
		/*
		for(var x=0;length;x++)
		{
			alert(x);
			$el = $('#movie-form .checkbox input[value='+movie.genre[x]+']');
			
			
		}
		*/
		
	},
	
	clearForm: function()
	{
		$('#movie-id').val('0');
		$('#movie-name').val('');
		$('#movie-cover').val('');
		$('#movie-desc').val('');
		$('#movie-trailer').val('');
		$('#movie-form .checkbox input').each(function(){
			this.checked = false;
		});
	},

	initTable: function()
	{
		$('#movie-table').tablesorter();
	},
	
	detail: function(id)
	{
		movie.killTrailer();
		ajax.req('detail',{
			data: {id:id},
			success: function(ret)
			{
				if(ret.movie != undefined)
				{					
					var cover ='';
					if(ret.movie.cover.length > 6)
					{
						cover = '<img style="margin:0 10px 10px 0;" class="img-rounded pull-left" src="/img/cover/r-150-' + ret.movie.cover + '" />';
					}
					
					var content = cover + '<p>' + nl2br(ret.movie.desc) + '</p><div class="clearfix"></div>';
					
					movie.$detail_body.html(content);
					movie.$detail_name.html(ret.movie.name);
					movie.$detail_trailer.html(ret.movie.trailer);
					movie.active = ret.movie;
					
					if(ret.movie.trailer.length > 6)
					{
						movie.$trailer_button.show();
						movie.$trailer_button.attr('href',ret.movie.trailer);
					}
					else
					{
						movie.$trailer_button.attr('href','#');
						movie.$trailer_button.hide();
					}
					
					movie.$detail_modal.modal('show');
				}
			}
		});
	},
	
	list: function()
	{
		loader();
		this.$table.html('');
		$.ajax({
			url: '/xhr.php?a=loadmovies',
			type: 'get',
			data: $('#movie-form').serialize(),
			success: function(ret)
			{
				if(ret.movies != undefined)
				{
					for(var i=0;i<ret.movies.length;i++)
					{
						movie.append(ret.movies[i]);
					}
					
					movie.initTable();
				}
			}
		});
	},
	
	append: function(movie)
	{
		var cover = '';
		
		if(movie.cover.length > 6)
		{
			cover = '<a href="#" onclick="movie.detail(\''+movie.id+'\');return false;"><img src="/img/cover/c-30-' + movie.cover + '" alt="' + movie.name + '" class="img-thumbnail"></a>';
		}
		
		this.$table.prepend('<tr class="g-'+movie.genre.join(' g-')+'" id="m-' + movie.id + '"><td>' + cover + '</td><td><a href="#" onclick="movie.detail(\''+movie.id+'\');return false;">' + movie.name + '</a></td><td width="200">' + movie.genre.join(', ') + '</td><td align="right"><div class="btn-group" role="group" aria-label="..."> ' +
				  //'<button onclick="movie.del(\'' + movie.id + '\');" type="button" class="btn btn-default"><i class="fa fa-times"></i></button>' +
				  '<button onclick="movie.edit(\'' + movie.id + '\');" type="button" class="btn btn-default"><i class="fa fa-pencil-square-o"></i></button>' +
				'</div></td></tr>');
	}
}
function error(msg)
{
	$.jGrowl(msg);
}
function info(msg)
{
	$.jGrowl(msg);
}

function nl2br(str, is_xhtml) 
{
	  var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br ' + '/>' : '<br>'; // Adjust comment to avoid issue on phpjs.org display

	  return (str + '')
	    .replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}

function loader(el)
{
	if(el != undefined)
	{
		var height = (parseInt(el.height())+12);
		el.after('<button style="height:'+height+'px;" type="button" class="btn btn-lg btn-primary button-loader" disabled="disabled"><i style="line-height:'+height+'px;" class="fa fa-spinner fa-spin"></i></button>');
		el.hide();
	}
}

function stopLoader(el)
{
	$('.button-loader').remove();
	if(el != undefined)
	{
		el.show();
	}
}

function youtube_parser(url)
{
    var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
    var match = url.match(regExp);
    if (match&&match[7].length==11){
        return match[7];
    }
    
    return false;
}