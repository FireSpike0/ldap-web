<?php
header("Content-Type: text/xml");
include ("/var/www/html/usermgmt/.init.php");

ldap_bind($ds, $serviceBindDN, $serviceBindPassword);

$username = "FEHLER___EMAIL_NICHT_GEFUNDEN";
$userDisplayName = "FEHLER: Benutzer nicht gefunden";
$postxml = file_get_contents("php://input");
if (1 === preg_match( "#<emailaddress>([a-zA-Z0-9.@-]+)</emailaddress>#i", $postxml, $emailaddress )) {
  $searchFor = ldap_escape($emailaddress[1], null, LDAP_ESCAPE_FILTER);
  $sr = ldap_search($ds, $peopleBase, "(mail=$searchFor)", [ "uid","displayName","mobile","homePhone" ]);
  $users = ldap_get_entries($ds, $sr);

  $username = "FEHLER___BENUTZER_NICHT_GEFUNDEN";
  if ($users["count"] == 1) {
    $username = $users[0]["uid"][0];
    $userDisplayName = $users[0]["displayname"][0];
  }
}

?><?xml version="1.0" encoding="utf-8" ?>
<Autodiscover xmlns="http://schemas.microsoft.com/exchange/autodiscover/responseschema/2006">
<Response xmlns="http://schemas.microsoft.com/exchange/autodiscover/outlook/responseschema/2006a">

<User>
<DisplayName><?= $userDisplayName ?></DisplayName>
</User>

<Account>
  <AccountType>email</AccountType>
  <Action>settings</Action>

  <Protocol>
    <Type>IMAP</Type>
    <Server>mail.d120.de</Server>
    <Port>993</Port>
    <DomainRequired>off</DomainRequired>
    <LoginName><?= $username ?></LoginName>
    <SPA>off</SPA>
    <SSL>on</SSL>
    <AuthRequired>on</AuthRequired>
  </Protocol>

  <Protocol>
    <Type>SMTP</Type>
    <Server>mail.d120.de</Server>
    <Port>587</Port>
    <DomainRequired>off</DomainRequired>
    <LoginName><?= $username ?></LoginName>
    <SPA>off</SPA>
    <SSL>on</SSL>
    <AuthRequired>on</AuthRequired>
  </Protocol>

</Account>
</Response>
</Autodiscover>

