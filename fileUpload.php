<?php
require_once("functions.php");
session_start();
//include_once("listing.php");
header('Content-Type: text/html; charset=utf-8');

global $CLIENT_ID, $CLIENT_SECRET, $REDIRECT_URI;
$client = new Google_Client();
$client->setClientId($CLIENT_ID);
$client->setClientSecret($CLIENT_SECRET);
$client->setRedirectUri($REDIRECT_URI);
$client->setScopes('email');

$Initialize = 0;  //1 = get credantial url and user information for first time  where as 0 means no need to initialize again
if($Initialize == 1)
{
$authUrl = $client->createAuthUrl();	  /// get credantial url and user information for first time
getCredentials($_GET['code'], $authUrl);    /// get credantial url and user information for first time
//getCredentials($_GET['4/nOnt557-K_YsJUQ0FV-8AT7yUBJtA0ygk5hVNBQnuFM#'], $authUrl);
$userName = $_SESSION["userInfo"]["name"];
$userEmail = $_SESSION["userInfo"]["email"];
}
//$service = new Google_Service_Drive($client);
//$FoldersSevice = getSOFolderByName($service);

//function getSOFolderByName($FoldersSevice)
//{
	
		//$folderID = GetFolderName($FoldersSevice);
//}

?>
<!DOCTYPE html>
<html lang="fi">
<head>
	<title>Logged in</title>
	<meta charset="UTF-8">
</head>
<body> 

	Hello <? echo $userName; ?>!
	<br>
	<? echo $userEmail; ?>

	<br><br><br>

	<form enctype="multipart/form-data" action="GDrive.php" method="POST">
    <table>
    <tr><td>Root Folder</td>
    <td><input type="text" id="root" name="folderName" value="MainFolder" readonly></td>
    <td><input type="text" id="root" name="folderDesc" value="SocietyFolder" readonly></td>
     <td><input type="file" name="file" required></td>
    </tr>
    <tr>
    <td align="left">SubFolder1</td>
    
    <td><input type="text" name="Sub_folderName[]" placeholder="My cat Whiskers"></td>
    <td><input type="text" name="Sub_folderDesc[]" placeholder="Folder descriptions"></td>
   
    </tr>
   <tr>
    <td align="left">FolderName2</td>
    <td><input type="text" name="Sub_folderName[]" placeholder="My cat Whiskers"></td>
    <td><input type="text" name="Sub_folderDesc[]" placeholder="Folder descriptions"></td>
    <!--<td><input type="file" name="file_array[]" required></td>
-->    </tr>
    <tr>
    <td align="left">FolderName3</td>
    <td><input type="text" name="Sub_folderName[]" placeholder="My cat Whiskers">/td>
    <td><input type="text" name="Sub_folderDesc[]" placeholder="Folder descriptions"></td>
    <!--<td><input type="file" name="file_array[]" required></td>-->
    </tr>
    <!--<input type="file" name="file" required>
		<br><br>
		<br><br>
		<label for="folderName">Folder name, this either uploads file to an existing folder or creates a new one based on the name</label>
		<br>
		<input type="text" name="folderName" placeholder="My cat Whiskers">
		<br><br>
		<label for="folderDesc">Folder description metadata, optional</label>
		<br>
		<input type="text" name="folderDesc" placeholder="Pictures of my cat">
		<br><br>
        <br><br>
		<label for="folderDesc">Folder description metadata, optional</label>
		<br>-->
        <tr>
        <td>Select Account Type</td>
        <td>
		<select name="IdType" id="IdType">
        <option value="0">sujit</option>
        <option value="1">Ayush</option>
        </select>
        </td></tr> 
        <tr><td><br></td></tr>
        <tr><td colspan="3" align="center">
		<input type="submit" name="submit" value="Upload to Drive">
        </td></tr></table>
        
	</form>
    <a href="listing.php"> Click Now </a>
</body>
</html>