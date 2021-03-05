<?php
// Copyright 2020 Catchpoint Systems Inc.
// Use of this source code is governed by the Polyform Shield 1.0.0 license that can be
// found in the LICENSE.md file.
chdir('..');
include 'common.inc';
$loc = GetDefaultLocation();
$tid= array_key_exists('tid', $_GET) ? $_GET['tid'] : 0;
$run= array_key_exists('run', $_GET) ? $_GET['run'] : 0;
$page_keywords = array('Video','comparison','WebPageTest','Website Speed Test');
$page_description = "Visually compare the performance of multiple websites with a side-by-side video and filmstrip view of the user experience.";
$profiles = null;
$profile_file = __DIR__ . '/../settings/profiles.ini';
if (file_exists(__DIR__ . '/../settings/common/profiles.ini'))
  $profile_file = __DIR__ . '/../settings/common/profiles.ini';
if (file_exists(__DIR__ . '/../settings/server/profiles.ini'))
  $profile_file = __DIR__ . '/../settings/server/profiles.ini';
if (is_file($profile_file))
  $profiles = parse_ini_file($profile_file, true);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>WebPageTest - Visual Comparison</title>
        <?php $gaTemplate = 'Visual Test'; include ('head.inc'); ?>
    </head>
    <body class="home">
            <?php
            $siteKey = GetSetting("recaptcha_site_key", "");
            if (!isset($uid) && !isset($user) && !isset($USER_EMAIL) && strlen($siteKey)) {
              echo "<script src=\"https://www.google.com/recaptcha/api.js\" async defer></script>\n";
              ?>
              <script>
              function onRecaptchaSubmit(token) {
                var form = document.getElementById("urlEntry");
                if (ValidateInput(form)) {
                  form.submit();
                } else {
                  grecaptcha.reset();
                }
              }
              </script>
              <?php
            }
            $tab = 'Home';
            include 'header.inc';
            ?>
            <h1 class="attention">Test. Optimize. Repeat.</h1>

            <form name="urlEntry" id="urlEntry" action="/video/docompare.php" method="POST" onsubmit="return ValidateInput(this)">


            <div id="test_box-container">
                <ul class="ui-tabs-nav">
                    <li class="analytical_review"><a href="/"><?php echo file_get_contents('./images/icon-advanced-testing.svg'); ?>Advanced Testing</a></li>
                    <?php
                    if (file_exists(__DIR__ . '/../settings/profiles.ini') ||
                        file_exists(__DIR__ . '/../settings/common/profiles.ini') ||
                        file_exists(__DIR__ . '/../settings/server/profiles.ini')) {
                        echo "<li class=\"easy_mode\"><a href=\"/easy\">";
                        echo file_get_contents('./images/icon-simple-testing.svg');
                        echo "Simple Testing</a></li>";
                    }
                    ?>
                    <li class="visual_comparison ui-state-default ui-corner-top ui-tabs-selected ui-state-active"><a href="#"><?php echo file_get_contents('./images/icon-visual-comparison.svg'); ?>Visual Comparison</a></li>
                    <li class="traceroute"><a href="/traceroute.php"><?php echo file_get_contents('./images/icon-traceroute.svg'); ?>Traceroute</a></li>
                </ul>
                <div id="visual_comparison" class="test_box">

                    <p>Enter multiple URLs to compare them against each other visually.</p>
                        <input type="hidden" id="nextid" value="2">
                        <div id="urls">
                            <?php
                            if( $tid )
                            {
                                $testPath = './' . GetTestPath($tid);
                                $pageData = loadAllPageData($testPath);
                                $url = trim($pageData[1][0]['URL']);
                                $testInfo = GetTestInfo($tid);
                                $label = trim($testInfo['label']);
                                if( strlen($url) )
                                {
                                    echo '<div id="urldiv0" class="urldiv">';
                                    echo "<input type=\"hidden\" id=\"tid\" name=\"tid\" value=\"$tid\">";
                                    echo "<input type=\"hidden\" id=\"run\" name=\"run\" value=\"$run\">";
                                    echo "Label: <input id=\"tidlabel\" type=\"text\" name=\"tidlabel\" value=\"$label\" style=\"width:10em\"> ";
                                    echo "URL: <input id=\"tidurl\" type=\"text\" style=\"width:30em\" value=\"$url\" disabled=\"disabled\"> ";
                                    echo "<a href='#' onClick='return RemoveUrl(\"#urldiv0\");'>Remove</a>";
                                    echo "</div>\n";
                                }
                            }
                            ?>
                            <div id="urldiv1" class="urldiv">
                                Label: <input id="label1" type="text" name="label[1]" style="width:10em">
                                URL: <input id="url1" type="text" name="url[1]" style="width:30em" onkeypress="if (event.keyCode == 32) {return false;}" >
                                <a href='#' onClick='return RemoveUrl("#urldiv1");'>Remove</a>
                            </div>
                        </div>
                        <br>
                        <button onclick="return AddUrl();">Add</button> another page to the comparison.
                        <br>
                        <br>
                        <br>
                        <br>
                        <ul>
                        <?php
                        if (isset($profiles) && is_array($profiles) && count($profiles)) {
                          echo '<li>';
                          echo '<label for="profile">Test Configuration:</label>';
                          echo '<select name="profile" id="profile" onchange="profileChanged()">';
                          foreach($profiles as $name => $profile) {
                            $selected = '';
                            if ($name == $_COOKIE['testProfile'])
                              $selected = 'selected';
                            echo "<option value=\"$name\" $selected>{$profile['label']}</option>";
                          }
                          if (isset($lastGroup))
                              echo "</optgroup>";
                          echo '</select>';
                          echo '</li>';
                          echo '<br>';
                          echo '<li>';
                          echo '<div id="description"></div>';
                          echo '</li>';
                          echo '</ul>';
                        }
                        ?>
                        <p id="footnote" class="cleared">For each URL, 3 first-view tests will be run from '<?php echo $loc['label']; ?>' and the median run will be used for comparison.
                        If you would like to test with different settings, submit your tests individually from the
                        <a href="/">main test page</a>.</p>
                    </div>
                </div>

                <div id="start_test-container">
                <?php
                            if (strlen($siteKey)) {
                            echo "<p><button data-sitekey=\"$siteKey\" data-callback='onRecaptchaSubmit' class=\"g-recaptcha start_test\"></button></p>";
                            } else {
                            echo '<input type="submit" name="submit" value="Start Test" class="start_test">';
                            }
                            ?>
                </div>
                <div class="cleared"></div>

                <script type="text/javascript">
                <?php
                  echo "var profiles = " . json_encode($profiles) . ";\n";
                ?>
                var wptStorage = window.localStorage || {};
                if (wptStorage['testrv'] != undefined)
                  $('#rv').prop('checked', wptStorage['testrv']);
                var rvChanged = function() {
                  wptStorage['testrv'] = $('#rv').is(':checked');
                }

                var profileChanged = function() {
                  var sel = document.getElementById("profile");
                  var txt = document.getElementById("description");
                  var profile = sel.options[sel.selectedIndex].value;
                  var description = "";
                  if (profiles[profile] !== undefined) {
                    var d = new Date();
                    d.setTime(d.getTime() + (365*24*60*60*1000));
                    document.cookie = "testProfile=" + profile + ";" + "expires=" + d.toUTCString() + ";path=/";
                    if (profiles[profile]['description'] !== undefined)
                      description = profiles[profile]['description'];
                  }
                  txt.innerHTML = description;
                };
                profileChanged();
                </script>
            </form>
            <?php include('home-subsections.inc'); ?>                
            <?php include('footer.inc'); ?>
        </div>

        <script type="text/javascript" src="<?php echo $GLOBALS['cdnPath']; ?>/video/videotest.js"></script>
    </body>
</html>
