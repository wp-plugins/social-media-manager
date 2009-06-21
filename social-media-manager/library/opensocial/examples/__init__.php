<?php
/*
 * Copyright 2008 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

// Set the default timezone since many servers won't have this configured
date_default_timezone_set('America/Los_Angeles');

// Report everything, better to have stuff break here than in production
ini_set('error_reporting', E_ALL | E_STRICT);

// Add the osapi directory to the include path
set_include_path(get_include_path() . PATH_SEPARATOR . '..');

// Require the osapi library
require_once "osapi/osapi.php";

// Allow users to select which test they would like to run from the querystring
if (isset($_REQUEST["test"])) {
  $test = $_REQUEST["test"];
} else {
  $test = 'orkut';
}

$osapi = false;
$strictMode = true;
$userId = '@me';
$appId = '@app';

// Create an identifier for the local user's session
session_start();
$localUserId = session_id();

// Select the appropriate test and initialize
switch ($test) {
  case 'XRDS':
    $storage = new osapiFileStorage('/tmp/osapi');
    $provider = new osapiXrdsProvider('http://www.partuza.nl/', $storage);
    $auth = osapiOAuth3Legged::performOAuthLogin('ddf4f9f7-f8e7-c7d9-afe4-c6e6c8e6eec4', '6f0e1a11ac45caed32d699f9e92ae959', $storage, $provider, $localUserId);
    $osapi = new osapi($provider, $auth);
    break;
  case 'partuza':
    $provider = new osapiPartuzaProvider();
    $osapi = new osapi($provider, osapiOAuth3Legged::performOAuthLogin('ddf4f9f7-f8e7-c7d9-afe4-c6e6c8e6eec4', '6f0e1a11ac45caed32d699f9e92ae959', new osapiFileStorage('/tmp/osapi'), $provider, $localUserId));
    break;
  case 'partuzaRest':
    $provider = new osapiPartuzaProvider();
    $provider->rpcEndpoint = null;
    $osapi = new osapi($provider, osapiOAuth3Legged::performOAuthLogin('ddf4f9f7-f8e7-c7d9-afe4-c6e6c8e6eec4', '6f0e1a11ac45caed32d699f9e92ae959', new osapiFileStorage('/tmp/osapi'), $provider, $localUserId));
    break;
  case 'partuzaLocal':
    $osapi = new osapi($provider = new osapiLocalPartuzaProvider(), osapiOAuth3Legged::performOAuthLogin('ddf4f9f7-f8e7-c7d9-afe4-c6e6c8e6eec4', '6f0e1a11ac45caed32d699f9e92ae959', new osapiFileStorage('/tmp/osapi'), $provider, $localUserId));
    break;
  case 'partuzaLocalRest':
    $provider = new osapiLocalPartuzaProvider();
    $provider->rpcEndpoint = null;
    $osapi = new osapi($provider, osapiOAuth3Legged::performOAuthLogin('ddf4f9f7-f8e7-c7d9-afe4-c6e6c8e6eec4', '6f0e1a11ac45caed32d699f9e92ae959', new osapiFileStorage('/tmp/osapi'), $provider, $localUserId));
    break;
  case 'plaxo':
    $osapi = new osapi($provider = new osapiPlaxoProvider(), osapiOAuth3Legged::performOAuthLogin('anonymous', '', new osapiFileStorage('/tmp/osapi'), $provider, $localUserId));
    break;
  case 'orkut':
    $userId = '03067092798963641994';
    $osapi = new osapi(new osapiOrkutProvider(), new osapiOAuth2Legged("orkut.com:623061448914", "uynAeXiWTisflWX99KU1D2q5", '03067092798963641994'));
    break;
  case 'orkutRest':
    $userId = '03067092798963641994';
    $provider = new osapiOrkutProvider();
    $provider->rpcEndpoint = null;
    $osapi = new osapi($provider, new osapiOAuth2Legged("orkut.com:623061448914", "uynAeXiWTisflWX99KU1D2q5", '03067092798963641994'));
    break;
  case 'myspace':
    $userId = '439607992';
    $osapi = new osapi(new osapiMySpaceProvider(), new osapiOAuth2Legged("http://dev.gain.resource.com", "7ebda6dee096455889bdab23ddacdfae", '439607992'));
}

$script_name = $_SERVER["SCRIPT_NAME"];
$tests = Array(
    "myspace"   => "MySpace",
    "orkut"     => "orkut",
    "orkutRest" => "orkut (REST)",
    "partuza"   => "Partuza",
    "plaxo"     => "Plaxo"
);

$links = Array();
foreach ($tests as $value => $name) {
  if ($value == $test) {
    $links[] = "<strong>$name</strong>";
  } else {
    $links[] = "<a href='$script_name?test=$value'>$name</a>";
  }
}

?>
<p><a href="index.php">Back to the index</a>. Run this sample using data from: <?= implode($links, ", ") ?> </p>
