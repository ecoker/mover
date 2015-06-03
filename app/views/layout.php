
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
  .breadcrumbs + .panel {
    background:#FFF;
    border-top:none;
  }
  .panel > a { display:block; margin:0; line-height:1.25em;  }
  .panel label, .panel input[type="checkbox"], td input[type="checkbox"] {
    margin:0;
    line-height:1.5em;
  }
  </style>
  <script src="js/vendor/custom.modernizr.js"></script>

</head>
<body>
  <div class="row">
    <div class="large-12 columns">
        <h4 class="subheader">File Mover</h4>
    </div>
    <div class="large-8 columns">
        <table width="100%">
            <thead>
                <tr>
                    <th>Sites</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tr>
                <td><input type="checkbox"> Shoes</td>
                <td><input type="checkbox"> Lifestride</td>
                <td><input type="checkbox"> Carlos Shoes</td>
                <td><input type="checkbox"> Dr. Scholls</td>
                <td><input type="checkbox"> Famous Footwear</td>
            <tr>
                <td><input type="checkbox"> Via Spiga</td>
                <td><input type="checkbox"> ShoeSteal</td>
                <td><input type="checkbox"> Naturalizer</td>
                <td><input type="checkbox"> Fergie</td>
                <td><input type="checkbox"> NaturalizerCA</td>
            </tr>
            <tr>
                <td><input type="checkbox"> NayaShoes</td>
                <td><input type="checkbox"> FrancoSarto</td>
                <td><input type="checkbox"> Ryka</td>
                <td><input type="checkbox"> Nevados</td>
                <td></td>
            </tr>
        </table>
    </div>
    <div class="large-4 columns">
        <table width="100%">
            <thead>
                <th>Environment</th>
            </thead>
            <tr>
                <td><input type="checkbox"> Local</td>
            </tr>
            <tr>
                <td><input type="checkbox"> Stage</td>
            </tr>
            <tr>
                <td><input type="checkbox"> Prod</td>
            </tr>
        </table>
    </div>
  </div>
  <div class="row">
    <div class="large-8 columns">
        <ul class="breadcrumbs">
          <li><a href="#">Sites</a></li>
          <li><a href="#">Folder</a></li>
          <li><a href="#">Folder</a></li>
          <li class="current"><a href="#">Current Folder</a></li>
        </ul>
        <div class="panel">
            <!-- <p class="subheader">Just testing this out...</p> -->
            <a href="#">Folder Name</a>
            <a href="#">Folder Name</a>
            <a href="#">Folder Name</a>
            <label><input type="checkbox"> File Name</label>
            <label><input type="checkbox"> File Name</label>
            <label><input type="checkbox"> File Name</label>
            <label><input type="checkbox"> File Name</label>
        </div>
    </div>
    <div class="large-4 columns">
        <a href="#" class="small button">Push Updates</a>
        <label for="checkbox1">
            <input type="checkbox" id="checkbox1" checked> Overwrite Older Files
        </label>
        <label for="checkbox2">
            <input type="checkbox" id="checkbox2"> Overwrite Newer Files
        </label>
    </div>
  </div>

  <script>
  document.write('<script src=js/vendor/' +
  ('__proto__' in {} ? 'zepto' : 'jquery') +
  '.js><\/script>')
  </script>
  <script src="js/foundation.min.js"></script>
  <script>
    $(document).foundation();
  </script>

</body>
</html>