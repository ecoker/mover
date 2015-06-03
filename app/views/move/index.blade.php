
<!DOCTYPE html>
<!--[if IE 8]>    <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8" />

  <!-- Set the viewport width to device width for mobile -->
  <meta name="viewport" content="width=device-width" />

  <title>File Mover</title>

  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/foundation.css">
  <style>
  .large-8 .breadcrumbs {
    border-radius:0;
    margin-bottom:0;
  }
  #file_browser { position:relative; }
  #file_browser > input[type="checkbox"] {
  	position: absolute;
		right: 2em;
		margin-top: .7em;
  }
  .breadcrumbs + .panel {
    background:#FFF;
    border-top:none;
  }
  .panel > a { display:block; margin:0; line-height:1.23em;  }
  .panel label, .panel input[type="checkbox"], td input[type="checkbox"] {
    margin:0;
    line-height:1.3em;
  }
  .browser { font-size:.9em; }
  .browser .even { background:#F3F3F3; }
  #features {
  	position:absolute;
  	top:.8em;
  	right:14px;
  	font-size:.8em;
  	z-index:1;
  }
  #environments label, #sites label {
  	font-size: inherit;
	font-weight: inherit;
	margin: inherit;
	color: inherit;
  }
  </style>
  <script src="js/vendor/custom.modernizr.js"></script>

</head>
<body>
	<div id="features">
		<input type="checkbox" name="multimove" id="multimove" /> multi-move
		<input type="checkbox" name="autodetect" id="autodetect" /> auto detect
	</div>
  <div class="row">
    <div class="large-12 columns">
        <h4 class="subheader">File Mover</h4>
        @foreach($errors as $error)
        {{ $error }}
        @endforeach
    </div>
    <div class="large-8 columns" id="sites">
        <table width="100%">
            <thead>
                <tr>
                    <th>Sites</th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            {{-- THIS LOOPS THROUGH THE LOCATIONS AND MAKES FANCY TIME --}}
            @for($i = 1; $i<=ceil(count($push_locations)/3); $i++)
            	<tr>
            	@foreach( array_slice($push_locations, (3*($i-1)), 3)  as $site => $e)
            		<td><label><input type="checkbox" {{ $push_locations[$site]['data'] }}> {{ ucwords($site) }}</label></td>
            	@endforeach
            	@if( count(array_slice($push_locations, (3*($i-1)), 3)) < 3 )
            		@for($j=1;$j<=(3-count(array_slice($push_locations, (3*($i-1)), 3)));$j++)
            		<td></td>
            		@endfor
            	@endif
            	</tr>
            @endfor
        </table>
    </div>
    <div id="environments" class="large-4 columns">
        <table width="100%">
            <thead>
                <th>Environment</th>
            </thead>
            @foreach($environments as $environment => $path)
	            @if ($environment !== 'data')
	            <tr>
	                <td><label><input type="checkbox" value="{{$path}}" data-name="{{ strtolower($environment) }}"> {{$environment}}</label></td>
	            </tr>
	            @endif
            @endforeach
        </table>
    </div>
  </div>
  <div class="row">
    <div id="file_browser" class="large-8 columns">
        <div id="results"></div>
        <input id="checkToggle" type="checkbox" checked>
        <ul class="breadcrumbs">
          <li><a href="#">Sites</a></li>
          <li><a href="#">Folder</a></li>
          <li><a href="#">Folder</a></li>
          <li class="current"><a href="#">Current Folder</a></li>
        </ul>
        <div class="panel browser"></div>
    </div>
    <div class="large-4 columns">
        <a id="push" href="#" class="small button">Push Updates</a>
        <label for="checkbox1">
            <input type="checkbox" name="overwrite-old" checked> Overwrite Older Files
        </label>
        <label for="checkbox2">
            <input type="checkbox" name="overwrite-new"> Overwrite Newer Files
        </label>
    </div>
  </div>

  <script>
  document.write('<script src=js/vendor/' +
  ('__proto__' in {} ? 'zepto' : 'jquery') +
  '.js><\/script>')
  </script>
  <script src="js/foundation.min.js"></script>
  <script src="js/vendor/p.cookie.js"></script>
  <script>
    $(document).foundation();
  </script>
  <script>
  if ( $.cookie('autodetect') ) {
  	$('#autodetect').prop('checked', true);
  }
  if ( $.cookie('multimove') ) {
  	$('#multimove').prop('checked', true);
  }
  if ( $.cookie('multimove') ) {
  	$('#multimove').prop('checked', true);
  }
  $('#sites input[type="checkbox"]').change(function(){
  	if ( !$('#multimove').is(':checked') ) {
  		$('#sites').find('input[type="checkbox"]').filter(':checked').not(this).prop('checked', false);
  	}
  	if ($('#sites input').filter(':checked').length == 1 && $('#autodetect').is(':checked') && $('#sites input').filter(':checked').length == 1) {
  		var path = $('input[data-name="local"]').val() +  $('#sites input').filter(':checked').first().attr('data-local');
  		$.cookie('autodir', path.replace('file://', ''));
  		updateFiles( '' );
  	}
  });
  $('#multimove').change(function(){
  	if ( $(this).is(':checked') ) {
  		$.cookie('multimove', true);
  	} else {
  		$.removeCookie('multimove');
  	}
  });
  $('#autodetect').change(function(){
  	if ( $(this).is(':checked') ) {
  		$.cookie('autodetect', true);
  		if ($('#sites input').filter(':checked').length > 0) {
  			var path = $('input[data-name="local"]').val() +  $('#sites input').filter(':checked').first().attr('data-local');
  			$.cookie('autodir', path.replace('file://', ''));
  			updateFiles( '' );
  		}
  	} else {
  		$.removeCookie('autodetect');
  	}
  });
  var active_folder = '';
  function updateFiles( path ) {
	  /* ANIMATION */
	  $('#file_browser').fadeTo(300, .4);

	  /* SET TO STORED DEFAULT PATH */
	  if ( path.length < 1 && $.cookie('autodir') && $.cookie('autodetect') ) { path = $.cookie('autodir'); }
	  /* SERVER CALL */
	  $.ajax({
		  type: "POST",
		  url: '/move',
		  data: $.cookie('autodetect') && path == $.cookie('autodir') ? {url:path, detect:'true'} : {url: path},
		  success: function(data){
		  	/* ANIMATION */
	  		$('#file_browser').fadeTo(300, 1);
		  	if ( data.detected && !$.cookie('autodir') ) {
					var tExpDate=new Date();
					tExpDate.setTime( tExpDate.getTime()+(3*60*1000) );
 					$.cookie('autodir', data.crumbs[ data.crumbs.length - 1 ]['path'], {'expires' : tExpDate});
		  	}
		  	var c = '';
		  	var m = '';
		  	if (typeof(data.crumbs) !== 'undefined'){
		  		var crumb_length = data.crumbs.length;
		  		var even = false;
          for(var i in data.crumbs){
		  			/* THIS IS SET TO AUTO DETECT & CHECK THE SOURCE BRAND FOLDER */
		  			var test_crumbs = data.crumbs[i]['path'].split('/');
            console.log(test_crumbs);
		  			if (test_crumbs[test_crumbs.length - 3] == '_svn') {
		  				$('input[data-local="' + test_crumbs[test_crumbs.length - 2] + '"]').prop('checked', true);
		  			}
		  			if (i < (crumb_length - 1)) {
		  				c += '<li><a href="' + data.crumbs[i]['path'] + '">' + data.crumbs[i]['folder'] + '</a></li>';
		  			} else {
		  				c += '<li class="current"><a href="' + data.crumbs[i]['path'] + '">' + data.crumbs[i]['folder'] + '</a></li>';
		  				var folders = data.crumbs[i]['path'].split('/');
		  				if ( $.inArray('Sites', folders) > -1 ) {
		  					if (folders.length >= $.inArray('_svn', folders) + 2){
		  						active_folder = '/' + folders.slice($.inArray('Sites', folders) + 2).join('/');
		  					} else {
		  						active_folder = '/';
		  					}
		  				} else {
		  					alert('Sorry, this only supports moving folders from the Sites directory.');
		  				}
		  			}
		  		}
		  	}
		  	if (typeof(data.dir) !== 'undefined'){
		  		for(var i in data.dir){
		  			m += '<a' + (even ? ' class="even"' : ' class="odd"') + ' href="' + data.dir[i]['path'] + '">' + data.dir[i]['name'] + '</a>';
		  			even = even ? false : true;
		  		}
		  	}
		  	if (typeof(data.file) !== 'undefined'){
		  		for(var i in data.file){
		  			m += '<label' + (even ? ' class="even"' : ' class="odd"') + '><input type="checkbox" value="' + data.file[i]['name'] + '" data-path="' + data.file[i]['path'] + '" ' + ($('#checkToggle').is(':checked') ? 'checked' : '') +'> ' + data.file[i]['name'] + '</label>';
		  			even = even ? false : true;
		  		}
		  	}

		  	$('#file_browser .breadcrumbs').html( c ).find('a').click(function(){
		  		updateFiles( $(this).attr('href') );
					event.preventDefault();
		  	});
		  	$('#file_browser .browser').html( m ).find('a').click(function(){
		  		updateFiles( $(this).attr('href') );
					event.preventDefault();
		  	});
		  },
		  dataType: 'json'
		});
  }
	updateFiles( '' );
	$('#checkToggle').change(function(){
		if ($(this).is(':checked')){
			$('.browser input[type="checkbox"]').prop('checked', true);		
		} else {
			$('.browser input[type="checkbox"]').prop('checked', false);
		}
	});
	$('a#push').click(function(){
		var moves     = {};
		moves['from'] = [];
		moves['to']   = [];
		var file_inputs       = $('.browser input[type="checkbox"]').filter(':checked');
		var move_environments = $('#environments input[type="checkbox"]').filter(':checked');
		var move_sites        = $('#sites input[type="checkbox"]').filter(':checked');
		if (file_inputs.length > 0 && move_environments.length > 0 && move_sites.length > 0) {
			$(file_inputs).each(function(){
				var file_location = 'file://' + $(this).val();
				var _file = $(this);
				$(move_sites).each(function(){
					var _site = $(this);
					$(move_environments).each(function(){
						var _environment = $(this);
						var site_location = _environment.val() + _site.attr('data-' + _environment.attr('data-name'));
						console.log( site_location, active_folder, _file.val() );
            var new_file_location = site_location + active_folder + _file.val();
						var move_data = [];
						move_data['from'] = 'file://' + _file.attr('data-path');
						move_data['to']   = new_file_location;
						if (move_data['from'] !== move_data['to']){
							moves['from'].push(move_data['from']);
							moves['to'].push(move_data['to']);
						}
					});
				});
			});
			moves['overwrite-old'] = $('input[name="overwrite-old"]').is(':checked') ? true : false;
			moves['overwrite-new'] = $('input[name="overwrite-new"]').is(':checked') ? true : false;
			$.ajax({
			  type: "POST",
			  url: '/move',
			  data: moves,
			  success: function(data){
			  	var r = '';
			  	for(var i in data.results){
			  		r += data.results[i];
			  	}
			  	$('#results').html( r ).find('a.close').click(function(){
			  		$(this).closest('.alert-box').fadeOut().remove();
			  	});
			  },
			  dataType: 'json'
			 });
		} else {
			if (file_inputs.length < 1) {
				alert('Please select a file to move.');
			} else if (move_sites.length < 1) {
				alert('Please select sites for the moving of files.');
			} else if (move_environments.length < 1) {
				alert('Plese select an environment to move files into.');
			}
		}
		return false;
	});
	</script>
</body>
</html>