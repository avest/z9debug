<?php
//===================================================================
// z9Debug
//===================================================================
// config_settings.sample.php
// --------------------
// Sample config settings
//
//       Date Created: 2018-03-05
//    Original Author: Allan Vest <al@z9digital.com>
//
// See the LICENSE file included with this program for additional
// licensing information.
//===================================================================

// OPTIONAL
// Remote authentication is ideal for when you want to use debug on
// multiple sites for the same set of developers.
// Specify the URL of a remote authentication API.
// For security reasons, make sure you use HTTPS.
// eg: https://<your domain>/<your_authentication_page>
// Z9 Debug will pass the username and password values from the login screen as POST variables to
// the URL specified.
// $_POST['developer_user']
// $_POST['developer_password']
// If the API request then returns a "1" value, the user will be authenticated.
debug::set('remote_authentication', '');

// If remote authentication is blank, then a single password can be used for authentication.
debug::set('password', '');

// REQUIRED
// A secret is used to encrypt the authentication token value saved to a cookie.
// Enter a random 8+ character value.
debug::set('secret', '');

// OPTIONAL
// If you want to populate user and page data to the "CMS" page in the console,
// set is_cms_installed to true and then call debug::set_cms_user() and/or
// debug::set_cms_page() to populate the data.
debug::set('is_cms_installed', false);

// It is recommended that force_http always be set to false.
// But if you truly need to acces the debug console and don't have HTTPS enabled, you
// can set force_http to true to bypass the HTTPS security check.
// Better yet, see https://letsencrypt.org to install a free SSL certificate on all of your development sites.
debug::set('force_http', false);

?>