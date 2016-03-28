<?php
class CedarCreateAccount extends SpecialPage
{
    function CedarCreateAccount()
    {
	SpecialPage::SpecialPage("CedarCreateAccount");
	#wfLoadExtensionMessages( 'CedarCreateAccount' ) ;
    }
    
    function execute( $par ) {
	global $wgRequest, $wgOut;
	
	$this->setHeaders();
	/*
	$wgOut->addHTML( "Account requests are not being accepted at this time" ) ;
	return ;
	*/
	
	# Get request data from, e.g.
	$param = $wgRequest->getText('status');
	if( $param == "submitted" )
	{
	    $this->submitted() ;
	}
	else if( $param == "create" )
	{
	    $this->createAccount() ;
	}
	else if( $param == "verify" )
	{
	    $this->verifyAccount() ;
	}
	else if( $param == "accepted" )
	{
	    $this->acceptNewAccount() ;
	}
	else if( $param == "deny" )
	{
	    $this->denyNewAccount() ;
	}
	else if( $param == "create" )
	{
	    $this->createAccount() ;
	}
	else
	{
	    $this->createAccountForm() ;
	}
    }

    function createAccountForm() {
	global $wgServer, $wgUser, $wgCedar, $wgRequest, $wgOut;

	$wgOut->addHTML( "<SPAN STYLE=\"font-weight:bold;font-size:16pt;\">CEDAR Database Access Form</SPAN>\n" ) ;
	$wgOut->addHTML( "<BR /><BR />\n" ) ;

	// does this user already have a wiki login?
	$loggedin = $wgUser->isLoggedIn() ;

	$cedarid = 0 ;
	if( $loggedin )
	{
	    // does this user already have a cedar login?
	    $wgCedar = CedarUser::newFromId( $wgUser->getID() ) ;
	    $cedarid = $wgCedar->getCedarId() ;
	}
	else
	{
	    $wgOut->addHTML( "If you already have a wiki login and are wanting access to the CEDAR Database, please login to the wiki and return to this page.<br><br> If you already have access to the CEDAR Database and you are wanting access to the wiki, you already have it. Use your Cedar Database username and password for access to the wiki.<br><br>" ) ;
	}

	// If they already have a cedar_user_info entry in the database then they will not be allowed to create a new account.
	if( $cedarid != 0 )
	{
	    $wgOut->addHTML( "You already have access to the CEDAR Database. Please <A HREF=\"mailto:cedar_db@hao.ucar.edu\">contact us</A> if you are having trouble accessing the database. If you are attempting to create a new account then please log out the current user." ) ;
	    return ;
	}

	$name = trim( $wgRequest->getVal( 'name', '' ) );
	$email = trim( $wgRequest->getVal( 'email', '' ) );
	$org = trim( $wgRequest->getVal( 'org', '' ) );
	$address1 = trim( $wgRequest->getVal( 'address1', '' ) );
	$address2 = trim( $wgRequest->getVal( 'address2', '' ) );
	$city = trim( $wgRequest->getVal( 'city', '' ) );
	$state = trim( $wgRequest->getVal( 'state', '' ) );
	$postal_code = trim( $wgRequest->getVal( 'postal_code', '' ) );
	$country = trim( $wgRequest->getVal( 'country', '' ) );
	$phone = trim( $wgRequest->getVal( 'phone', '' ) );
	$mobile_phone = trim( $wgRequest->getVal( 'mobile_phone', '' ) );
	$fax = trim( $wgRequest->getVal( 'fax', '' ) );
	$supervisor_name = trim( $wgRequest->getVal( 'supervisor_name', '' ) );
	$supervisor_email = trim( $wgRequest->getVal( 'supervisor_email', '' ) );
	$username = trim( $wgRequest->getVal( 'username', '' ) );
	$have_used = trim( $wgRequest->getVal( 'have_used', '' ) );
	$would_like = trim( $wgRequest->getVal( 'would_like', '' ) );

	# Output
	$wgOut->addScript( "<script language=\"javascript\">

	function enablesubmitbutton() {

	if (document.cedarcreate.agree.checked == true )
	{

	  document.cedarcreate.submit.disabled=false;

	}  else {
	  document.cedarcreate.submit.disabled=true;
	}
	}

	function fillinform() {

	if (document.cedarcreate.supervisor_name.value == \"\" )
	{
	    document.cedarcreate.supervisor_name.value = \"No Supervisor Specified\" ;
	}

	if (document.cedarcreate.supervisor_email.value == \"\" )
	{
	    document.cedarcreate.supervisor_email.value = \"No Supervisor Email Specified\" ;
	}
	}

	</script>\n" ) ;
	$wgOut->addHTML( " The CEDAR Database at NCAR contains documentation, indices, empirical models, model outputs, and data.  Web user login accounts are available to access the data using the World Wide Web. A complete inventory of the data is listed on the web at Documents/Catalog or click here for a version to print. The document is also available on <A HREF=\"ftp://ftp.hao.ucar.edu:122/archive/cedar/catmad.list\">FTP</A> or for <A HREF=\"http://download.hao.ucar.edu/archive/cedar/catmad.list\">download</A>.\n" ) ;

	$wgOut->addHTML( "<br><br>* indicates required frields" ) ;
	// if they already have a login this means they already have a wiki login and are wanting to upgrade to access the CEDAR database.
	if( $loggedin )
	{
	    $wgOut->addHTML( "<BR /><BR />\n" ) ;
	    $wgOut->addHTML( "You appear to already have a CedarWiki login.  This form will grant you access to the CEDAR database itself in addition to your wiki access.\n") ;
	}

	$wgOut->addHTML( "<FORM name=\"cedarcreate\" action=\"$wgServer/wiki/index.php/Special:Cedar_Create_Account?status=create\" method=\"POST\" onsubmit=\"fillinform()\">\n" ) ;
	#$wgOut->addHTML( "    <INPUT type=\"hidden\" name=\"recipient\" value=\"cedar_db@hao.ucar.edu\">\n" ) ;
	#$wgOut->addHTML( "    <INPUT type=\"hidden\" name=\"subject\" value=\"[CEDARWEB ACCESS FORM]: Application\">\n" ) ;
	#$wgOut->addHTML( "    <INPUT type=\"hidden\" name=\"required\" value=\"name,email,username,org,address1,city,state,postal_code,country\">\n" ) ;
	if( $loggedin )
	{
	    $wgOut->addHTML( "    <INPUT type=\"hidden\" name=\"upgrading\" value=\"true\">\n" ) ;
	}
	else
	{
	    $wgOut->addHTML( "    <INPUT type=\"hidden\" name=\"upgrading\" value=\"false\">\n" ) ;
	}
	$wgOut->addHTML( "    <BR />\n" ) ;
	$wgOut->addHTML( "    <TABLE ALIGN=\"LEFT\" BORDER=\"0\" WIDTH=\"660\" CELLPADDING=\"0\" CELLSPACING=\"0\">\n" ) ;
	if( !$loggedin || $wgUser->getRealName() == '' )
	{
	    $wgOut->addHTML( "	<TR>\n" ) ;
	    $wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"RIGHT\">\n" ) ;
	    $wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">Real Name*:&nbsp;&nbsp;</SPAN>\n" ) ;
	    $wgOut->addHTML( "	    </TD>\n" ) ;
	    $wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\">\n" ) ;
	    $wgOut->addHTML( "		<INPUT TYPE=\"text\" NAME=\"name\" value=\"$name\" SIZE=\"30\">\n" ) ;
	    $wgOut->addHTML( "	    </TD>\n" ) ;
	    $wgOut->addHTML( "	</TR>\n" ) ;
	}
	else
	{
	    $realname = $wgUser->getRealName() ;
	    $wgOut->addHTML( "	<TR>\n" ) ;
	    $wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"RIGHT\">\n" ) ;
	    $wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">Real Name:&nbsp;&nbsp;</SPAN>\n" ) ;
	    $wgOut->addHTML( "	    </TD>\n" ) ;
	    $wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\">\n" ) ;
	    $wgOut->addHTML( "		<INPUT DISABLED TYPE=\"text\" NAME=\"name\" VALUE=\"$realname\" SIZE=\"30\">\n" ) ;
	    $wgOut->addHTML( "          <INPUT type=\"hidden\" name=\"name\" value=\"$realname\">\n" ) ;
	    $wgOut->addHTML( "	    </TD>\n" ) ;
	    $wgOut->addHTML( "	</TR>\n" ) ;
	}
	if( !$loggedin || $wgUser->getEmail() == '' )
	{
	    $wgOut->addHTML( "	<TR>\n" ) ;
	    $wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"RIGHT\">\n" ) ;
	    $wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">Email*:&nbsp;&nbsp;</SPAN>\n" ) ;
	    $wgOut->addHTML( "	    </TD>\n" ) ;
	    $wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\">\n" ) ;
	    $wgOut->addHTML( "		<INPUT TYPE=\"text\" NAME=\"email\" value=\"$email\" SIZE=\"30\">\n" ) ;
	    $wgOut->addHTML( "	    </TD>\n" ) ;
	    $wgOut->addHTML( "	</TR>\n" ) ;
	}
	else
	{
	    $email = $wgUser->getEmail() ;
	    $wgOut->addHTML( "	<TR>\n" ) ;
	    $wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"RIGHT\">\n" ) ;
	    $wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">Email:&nbsp;&nbsp;</SPAN>\n" ) ;
	    $wgOut->addHTML( "	    </TD>\n" ) ;
	    $wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\">\n" ) ;
	    $wgOut->addHTML( "		<INPUT DISABLED TYPE=\"text\" NAME=\"email\" VALUE=\"$email\" SIZE=\"30\">\n" ) ;
	    $wgOut->addHTML( "          <INPUT type=\"hidden\" name=\"email\" value=\"$email\">\n" ) ;
	    $wgOut->addHTML( "	    </TD>\n" ) ;
	    $wgOut->addHTML( "	</TR>\n" ) ;
	}
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD COLSPAN=\"2\" WIDTH=\"100%\">\n" ) ;
	$wgOut->addHTML( "	        &nbsp;\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"RIGHT\">\n" ) ;
	$wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">Organization*:&nbsp;&nbsp;</SPAN>\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\">\n" ) ;
	$wgOut->addHTML( "		<INPUT TYPE=\"text\" NAME=\"org\" VALUE=\"$org\" SIZE=\"30\">\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"RIGHT\">\n" ) ;
	$wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">Address1*:&nbsp;&nbsp;</SPAN>\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\">\n" ) ;
	$wgOut->addHTML( "		<INPUT TYPE=\"text\" NAME=\"address1\" VALUE=\"$address1\" SIZE=\"30\">\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"RIGHT\">\n" ) ;
	$wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">Address2:&nbsp;&nbsp;</SPAN>\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\">\n" ) ;
	$wgOut->addHTML( "		<INPUT TYPE=\"text\" NAME=\"address2\" VALUE=\"$address2\" SIZE=\"30\">\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"RIGHT\">\n" ) ;
	$wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">City*:&nbsp;&nbsp;</SPAN>\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\">\n" ) ;
	$wgOut->addHTML( "		<INPUT TYPE=\"text\" NAME=\"city\" VALUE=\"$city\" SIZE=\"30\">\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"RIGHT\">\n" ) ;
	$wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">State*:&nbsp;&nbsp;</SPAN>\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\">\n" ) ;
	$wgOut->addHTML( "		<INPUT TYPE=\"text\" NAME=\"state\" VALUE=\"$state\" SIZE=\"30\">\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"RIGHT\">\n" ) ;
	$wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">Postal Code*:&nbsp;&nbsp;</SPAN>\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\">\n" ) ;
	$wgOut->addHTML( "		<INPUT TYPE=\"text\" NAME=\"postal_code\" VALUE=\"$postal_code\" SIZE=\"30\">\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"RIGHT\">\n" ) ;
	$wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">Country*:&nbsp;&nbsp;</SPAN>\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\">\n" ) ;
	$wgOut->addHTML( "		<INPUT TYPE=\"text\" NAME=\"country\" VALUE=\"$country\" SIZE=\"30\">\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD COLSPAN=\"2\" WIDTH=\"100%\">\n" ) ;
	$wgOut->addHTML( "	        &nbsp;\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"RIGHT\">\n" ) ;
	$wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">Phone:&nbsp;&nbsp;</SPAN>\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\">\n" ) ;
	$wgOut->addHTML( "		<INPUT TYPE=\"text\" NAME=\"phone\" VALUE=\"$phone\" SIZE=\"30\">\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"RIGHT\">\n" ) ;
	$wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">Mobile Phone:&nbsp;&nbsp;</SPAN>\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\">\n" ) ;
	$wgOut->addHTML( "		<INPUT TYPE=\"text\" NAME=\"mobile_phone\" VALUE=\"$mobile_phone\" SIZE=\"30\">\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"RIGHT\">\n" ) ;
	$wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">Fax:&nbsp;&nbsp;</SPAN>\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\">\n" ) ;
	$wgOut->addHTML( "		<INPUT TYPE=\"text\" NAME=\"fax\" VALUE=\"$fax\" SIZE=\"30\">\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD COLSPAN=\"2\" WIDTH=\"100%\">\n" ) ;
	$wgOut->addHTML( "	        &nbsp;\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"RIGHT\">\n" ) ;
	$wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">Supervisor Name:&nbsp;&nbsp;<BR />(if programmer/student)&nbsp;&nbsp;</SPAN>\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\">\n" ) ;
	$wgOut->addHTML( "		<INPUT TYPE=\"text\" NAME=\"supervisor_name\" VALUE=\"$supervisor_name\" SIZE=\"30\">\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"RIGHT\">\n" ) ;
	$wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">Supervisor Email:&nbsp;&nbsp;<BR />(if programmer/student)&nbsp;&nbsp;</SPAN>\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\">\n" ) ;
	$wgOut->addHTML( "		<INPUT TYPE=\"text\" NAME=\"supervisor_email\" VALUE=\"$supervisor_email\" SIZE=\"30\">\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD COLSPAN=\"2\" WIDTH=\"100%\">\n" ) ;
	$wgOut->addHTML( "	        &nbsp;\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	if( $loggedin )
	{
	    $localname = $wgUser->getName() ;
	    $wgOut->addHTML( "	<TR>\n" ) ;
	    $wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"RIGHT\">\n" ) ;
	    $wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">Your username is:&nbsp;&nbsp;</SPAN>\n" ) ;
	    $wgOut->addHTML( "	    </TD>\n" ) ;
	    $wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\">\n" ) ;
	    $wgOut->addHTML( "		<INPUT DISABLED TYPE=\"text\" NAME=\"username\" VALUE=\"$localname\" SIZE=\"30\">\n" ) ;
	    $wgOut->addHTML( "          <INPUT type=\"hidden\" name=\"username\" value=\"$localname\">\n" ) ;
	    $wgOut->addHTML( "	    </TD>\n" ) ;
	    $wgOut->addHTML( "	</TR>\n" ) ;
	}
	else
	{
	    $wgOut->addHTML( "	<TR>\n" ) ;
	    $wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"RIGHT\">\n" ) ;
	    $wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">Please select username*:&nbsp;&nbsp;</SPAN>\n" ) ;
	    $wgOut->addHTML( "	    </TD>\n" ) ;
	    $wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\">\n" ) ;
	    $wgOut->addHTML( "		<INPUT TYPE=\"text\" NAME=\"username\" VALUE=\"$username\" SIZE=\"30\">\n" ) ;
	    $wgOut->addHTML( "	    </TD>\n" ) ;
	    $wgOut->addHTML( "	</TR>\n" ) ;
	}
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD COLSPAN=\"2\" WIDTH=\"100%\">\n" ) ;
	$wgOut->addHTML( "	        &nbsp;\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD COLSPAN=\"2\" WIDTH=\"100%\" CLASS=\"contexttext\">\n" ) ;
	$wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">What data sets, models, or indices have you used from the CEDAR Database?</SPAN>\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD COLSPAN=\"2\" WIDTH=\"100%\" CLASS=\"contexttext\">\n" ) ;
	$wgOut->addHTML( "		<TEXTAREA NAME=\"have_used\" ROWS=\"2\" COLS=\"60\">$have_used</TEXTAREA>\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD COLSPAN=\"2\" WIDTH=\"100%\">\n" ) ;
	$wgOut->addHTML( "	        &nbsp;\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD COLSPAN=\"2\" WIDTH=\"100%\" CLASS=\"contexttext\">\n" ) ;
	$wgOut->addHTML( "		<SPAN STYLE=\"font-weight:bold;\">What data sets, models, or indices would you like to use?<BR /> (Including those not currently availiable.)</SPAN>\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD COLSPAN=\"2\" WIDTH=\"100%\" CLASS=\"contexttext\">\n" ) ;
	$wgOut->addHTML( "		<TEXTAREA NAME=\"would_like\" ROWS=\"2\" COLS=\"60\">$would_like</TEXTAREA>\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD COLSPAN=\"2\" WIDTH=\"100%\">\n" ) ;
	$wgOut->addHTML( "	        &nbsp;\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD COLSPAN=\"2\" WIDTH=\"100%\">\n" ) ;
	$wgOut->addHTML( "	        <INPUT NAME=\"agree\" TYPE=\"CHECKBOX\" onclick=\"enablesubmitbutton()\">&nbsp;I agree to abide by these <A HREF=\"$wgServer/wiki/index.php/Data_Services:Rules_of_the_Road\" TARGET=\"cedar_wiki_aux\">Rules of the Road</A> for the CEDAR Database and have read the <A HREF=\"$wgServer/wiki/index.php/CedarWiki:Privacy_policy\" TARGET=\"cedar_wiki_aux\">UCAR Privacy Policy</A>\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD COLSPAN=\"2\" WIDTH=\"100%\">\n" ) ;
	$wgOut->addHTML( "	        &nbsp;\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "	<TR>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"30%\" CLASS=\"contexttext\" ALIGN=\"CENTER\">\n" ) ;
	$wgOut->addHTML( "              <INPUT TYPE=\"SUBMIT\" NAME=\"submit\" VALUE=\"Submit\" disabled>\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	    <TD WIDTH=\"70%\" CLASS=\"contexttext\" ALIGN=\"LEFT\">\n" ) ;
	$wgOut->addHTML( "              <INPUT TYPE=\"RESET\" VALUE=\"Reset\">\n" ) ;
	$wgOut->addHTML( "	    </TD>\n" ) ;
	$wgOut->addHTML( "	</TR>\n" ) ;
	$wgOut->addHTML( "    </TABLE>\n" ) ;
	$wgOut->addHTML( "</FORM>\n" ) ;
    }
    
    function submitted() {
	global $wgOut;
	$wgOut->addWikiText( "=Your information has been submitted=" ) ; 
	$wgOut->addWikiText( "<br />You should be receiving a confirmation email
	soon. Once submitted, you should receive an email regarding the status of your account. If accepted then you will be asked to login with the username you selected with a temporary password, and asked to change your password." ) ;
	$wgOut->addWikiText( "<br />Return to [[Main Page]]" ) ;
    }

    function verifyAccount() {
	global $wgOut, $wgServer, $wgRequest ;
	$username = $wgRequest->getText('wpName');
	$code = $wgRequest->getText('wpCode');
	$done = false ;
	if( !$username || $username == "" )
	{
	    $wgOut->addWikiText( "=Your information is missing the username=" ) ;
	    $done = true ;
	}
	if( !$code || $code == "" )
	{
	    $wgOut->addWikiText( "=Your information is missing the confirmation code=" ) ;
	    $done = true ;
	}

	// Now look up in the database for this username with this verification
	// code
	$dbh =& wfGetDB( DB_MASTER ) ;
	if( !$dbh )
	{
	    $wgOut->addHTML( "Failed to confirm user $username:<br />\n" ) ;
	    $wgOut->addHTML( "Unable to connect to the database:<br />\n" ) ;
	    $wgOut->addHTML( "<br />\n" ) ;
	    return ;
	}

	$cedar_user_table = $dbh->tableName( 'cedar_user_info' ) ;

	$tryusername = $dbh->strencode( strtoupper( $username ) ) ;
	$sql = "SELECT user_name,real_name,email,organization,address1,address2,city,state,postal_code,country,phone,mobile_phone,fax,supervisor_name,supervisor_email,comments FROM ".$cedar_user_table." WHERE ucase(user_name)=\"$tryusername\" && code=\"$code\"";
	$res = $dbh->query( $sql ) ;

	// no result
	if( !$res )
	{
	    $db_error = $dbh->lastError() ;
	    $wgOut->addHTML( "Failed to confirm user $username:<br />\n" ) ;
	    $wgOut->addHTML( "Unable to query the database:<br />\n" ) ;
	    $wgOut->addHTML( $db_error ) ;
	    $wgOut->addHTML( "<br />\n" ) ;
	    return ;
	}

	// zero or more than 1 result
	if( $res->numRows() != 1 )
	{
	    $wgOut->addHTML( "<span style=\"color:red\">The username $username does not exist. If you need help logging in, go to our <a href=\"http://cedarweb.hao.ucar.edu/wiki/index.php/Help:Username_and_Password\">user help page</a></span><br /><br />\n" ) ;
	    return ;
	}

	$obj = $dbh->fetchObject( $res ) ;
	if( !$obj )
	{
	    $db_error = $dbh->lastError() ;
	    $wgOut->addHTML( "Failed to confirm user $username:<br />\n" ) ;
	    $wgOut->addHTML( "Unable to query the database:<br />\n" ) ;
	    $wgOut->addHTML( $db_error ) ;
	    $wgOut->addHTML( "<br />\n" ) ;
	    return ;
	}

	$sql = "SELECT user_name,real_name,email,organization,address1,address2,city,state,postal_code,country,phone,mobile_phone,fax,supervisor_name,supervisor_email,comments FROM ".$cedar_user_table." WHERE ucase(user_name)=\"$tryusername\" && code=\"$code\"";
	$name = $obj->user_name ;
	$real_name = $obj->r_real_name ;
	$email = $obj->email ;
	$org = $obj->organization ;
	$addr1 = $obj->address1 ;
	$addr2 = $obj->address2 ;
	$city = $obj->city ;
	$state = $obj->state ;
	$country = $obj->country ;
	$zip = $obj->postal_code ;
	$phone = $obj->phone ;
	$mphone = $obj->mobile_phone ;
	$fax = $obj->fax ;
	$sname = $obj->supervisor_name ;
	$semail = $obj->supervisor_email ;
	$comments = $obj->comments ;
	$doupgrade = $obj->upgrading ;
	if( $doupgrade )
	{
	    $upgrading = "true" ;
	}
	else
	{
	    $upgrading = "false" ;
	}

	// Now that we have verified, let's take the informatioon and send it to
	// the cedardb email.
	require_once( 'UserMailer.php' ) ;
	$curr_date = date( "l, F d, Y h:i:s A" ) ;
	$to = new MailAddress( "cedar_db@hao.ucar.edu", "CEDAR DB" ) ;
	$from = new MailAddress( $email, $name ) ;
	$subject = "[CEDARWiki]: Access Application" ;

	$body = "Below is the result an account request form.  It was submitted by\n" ;
	$body .= "$name ($email) on $curr_date\n" ;
	$body .= str_repeat( "-", 75 ) . "\n\n" ;
	$body .= "To accept the new user, click on this link\n" ;
	$url_name = urlencode( $name ) ;
	$accept_url = "$wgServer/wiki/index.php/Special:Cedar_Create_Account?status=accepted&wpName=$username&wpRealName=$url_name&wpEmail=$email&wpUpgrade=$upgrading" ;
	$body .= "$accept_url\n\n" ;
	$body .= "To deny this new user, click on this link\n" ;
	$deny_url = "$wgServer/wiki/index.php/Special:Cedar_Create_Account?status=deny&wpName=$username" ;
	$body .= "$deny_url\n\n" ;
	$body .= str_repeat( "-", 75 ) . "\n\n" ;
	$body .= "Information entered by user\n" ;
	$body .= "upgrading: $upgrading\n\n" ;
	$body .= "name: $name\n\n" ;
	$body .= "org: $org\n\n" ;
	$body .= "address1: $address1\n\n" ;
	$body .= "address2: $address2\n\n" ;
	$body .= "city: $city\n\n" ;
	$body .= "state: $state\n\n" ;
	$body .= "postal_code: $postal_code\n\n" ;
	$body .= "country: $country\n\n" ;
	$body .= "phone: $phone\n\n" ;
	$body .= "mobile phone: $mobile_phone\n\n" ;
	$body .= "fax: $fax\n\n" ;
	$body .= "supervisor name: $supervisor_name\n\n" ;
	$body .= "supervisor email: $supervisor_email\n\n" ;
	$body .= "username: $username\n\n" ;
	$body .= "agree: $agree\n\n" ;
	$body .= "comments: $comments\n\n" ;
	$body .= str_repeat( "-", 75 ) ;

	$result = UserMailer::send( $to, $from, $subject, $body );

	if( $result && !$result->isOK() )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red;\">We were unable to send an email to the Cedar Administrator.\n" ) ;
	    $wgOut->addHTML( "Please send an email to cedardb@hao.ucar.edu and provide them with the username that you entered.\n" ) ;
	    $wgOut->addHTML( "Also provide us with any error message that you see below. Thank you<BR /><BR /></SPAN>\n" ) ;
	    $msg = $result->getMessage() ;
	    $wgOut->addHTML( "$msg\n\n" ) ;
	    return ;
	}

	$update_success = $dbh->update( "cedar_user_info",
		array(
			'status' => "verified",
		),
		array(
			'user_name' => $username,
			'code' => $code
		),
		__METHOD__
	) ;

	$wgOut->addWikiText( "=Your information has been confirmed=" ) ; 
	$wgOut->addWikiText( "<br />You should receive an email regarding the status of your account shortly. If accepted then you will be asked to login with the username you selected with a temporary password, and asked to change your password." ) ;
	$wgOut->addWikiText( "<br />Return to [[Main Page]]" ) ;
    }

    function createAccount()
    {
	global $wgUser, $wgRequest, $wgOut, $wgAuth, $wgServer ;

	$upgrading = trim( $wgRequest->getVal( 'upgrading' ) ) ;
	$name = trim( $wgRequest->getVal( 'name' ) ) ;
	$email = trim( $wgRequest->getVal( 'email' ) ) ;
	$org = trim( $wgRequest->getVal( 'org' ) ) ;
	$address1 = trim( $wgRequest->getVal( 'address1' ) ) ;
	$address2 = trim( $wgRequest->getVal( 'address2' ) ) ;
	$city = trim( $wgRequest->getVal( 'city' ) ) ;
	$state = trim( $wgRequest->getVal( 'state' ) ) ;
	$postal_code = trim( $wgRequest->getVal( 'postal_code' ) ) ;
	$country = trim( $wgRequest->getVal( 'country' ) ) ;
	$phone = trim( $wgRequest->getVal( 'phone' ) ) ;
	$mobile_phone = trim( $wgRequest->getVal( 'mobile_phone' ) ) ;
	$fax = trim( $wgRequest->getVal( 'fax' ) ) ;
	$supervisor_name = trim( $wgRequest->getVal( 'supervisor_name' ) ) ;
	$supervisor_email = trim( $wgRequest->getVal( 'supervisor_email' ) ) ;
	$username = ucfirst( trim( $wgRequest->getVal( 'username' ) ) ) ;
	$have_used = trim( $wgRequest->getVal( 'have_used', '' ) ) ;
	$would_like = trim( $wgRequest->getVal( 'would_like', '' ) ) ;
	$comments = "Have Used: $have_used  | Would Like: $would_like " ;
	$agree = trim( $wgRequest->getVal( 'agree', 'off' ) ) ;

	$found_errors = 0 ;
	if( !$name )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red\">Real Name must be specified</SPAN><BR />\n" ) ;
	    $found_errors++ ;
	}
	if( !$email )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red\">Email must be specified</SPAN><BR />\n" ) ;
	    $found_errors++ ;
	}
	$atpos=strpos($email,"@",0) ;
	if( !$atpos )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red\">Malformed email address</SPAN><BR />\n" ) ;
	    $found_errors++ ;
	}
	else
	{
	    $dotpos=strpos($email,".",$atpos) ;
	    if( !$dotpos )
	    {
		$wgOut->addHTML( "<SPAN STYLE=\"color:red\">Malformed email address</SPAN><BR />\n" ) ;
		$found_errors++ ;
	    }
	}
	if( !$org )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red\">Organization must be specified</SPAN><BR />\n" ) ;
	    $found_errors++ ;
	}
	if( !$address1 )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red\">Address must be specified</SPAN><BR />\n" ) ;
	    $found_errors++ ;
	}
	if( !$city )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red\">City must be specified</SPAN><BR />\n" ) ;
	    $found_errors++ ;
	}
	if( !$state )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red\">State must be specified</SPAN><BR />\n" ) ;
	    $found_errors++ ;
	}
	if( !$postal_code )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red\">Postal Code must be specified</SPAN><BR />\n" ) ;
	    $found_errors++ ;
	}
	if( !$country )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red\">Country must be specified</SPAN><BR />\n" ) ;
	    $found_errors++ ;
	}
	if( $phone )
	{
	    $badstuff = false ;
	    for( $i = 0; $i < strlen( $phone ); $i++ )
	    {
		if( ctype_alpha( $phone[$i] ) )
		{
		    $badstuff = true ;
		    break ;
		}
	    }
	    if( $badstuff )
	    {
		$wgOut->addHTML( "<SPAN STYLE=\"color:red\">Malformed phone number</SPAN><BR />\n" ) ;
		$found_errors++ ;
	    }
	}
	if( $mobile_phone )
	{
	    $badstuff = false ;
	    for( $i = 0; $i < strlen( $mobile_phone ); $i++ )
	    {
		if( ctype_alpha( $mobile_phone[$i] ) )
		{
		    $badstuff = true ;
		    break ;
		}
	    }
	    if( $badstuff )
	    {
		$wgOut->addHTML( "<SPAN STYLE=\"color:red\">Malformed mobile number</SPAN><BR />\n" ) ;
		$found_errors++ ;
	    }
	}
	if( $fax )
	{
	    $badstuff = false ;
	    for( $i = 0; $i < strlen( $fax ); $i++ )
	    {
		if( ctype_alpha( $fax[$i] ) )
		{
		    $badstuff = true ;
		    break ;
		}
	    }
	    if( $badstuff )
	    {
		$wgOut->addHTML( "<SPAN STYLE=\"color:red\">Malformed fax number</SPAN><BR />\n" ) ;
		$found_errors++ ;
	    }
	}
	if( $supervisor_email && $supervisor_email != "No Supervisor Email Specified" )
	{
	    $atpos=strpos($supervisor_email,"@",0) ;
	    if( !$atpos )
	    {
		$wgOut->addHTML( "<SPAN STYLE=\"color:red\">Malformed supervisor email address</SPAN><BR />\n" ) ;
		$found_errors++ ;
	    }
	    else
	    {
		$dotpos=strpos($supervisor_email,".",$atpos) ;
		if( !$dotpos )
		{
		    $wgOut->addHTML( "<SPAN STYLE=\"color:red\">Malformed supervisor email address</SPAN><BR />\n" ) ;
		    $found_errors++ ;
		}
	    }
	}
	if( !$username )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red\">Username must be specified</SPAN><BR />\n" ) ;
	    $found_errors++ ;
	}
	$tryusername = ucfirst( $username ) ;
	if( !User::isCreatableName( $tryusername ) )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red\">Username specified is not a valid wiki user name</SPAN><BR />\n" ) ;
	    $found_errors++ ;
	}
	if( strpos( $username, " " ) )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red\">Username specified can not contain spaces</SPAN><BR />\n" ) ;
	    $found_errors++ ;
	}
	if( $agree == "off" )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red\">You must accept the CedarDB Rules of the Road and have read the UCAR Privacy Policy</SPAN><BR />\n" ) ;
	    $found_errors++ ;
	}

	if( $found_errors > 0 )
	{
	    $wgOut->addHTML( "<BR>\n" ) ;
	    $this->createAccountForm() ;
	    return ;
	}

	// check the username and make sure it doesn't already exist
	$dbh =& wfGetDB( DB_MASTER ) ;
	if( !$dbh )
	{
	    $wgOut->addHTML( "Failed to create user $username:<BR />\n" ) ;
	    $wgOut->addHTML( "Unable to connect to the database:<BR />\n" ) ;
	    $wgOut->addHTML( "<BR />\n" ) ;
	    return ;
	}
	$cedar_user_table = $dbh->tableName( 'cedar_user_info' ) ;
	$user_table = $dbh->tableName( 'user' ) ;

	$tryusername = $dbh->strencode( strtoupper( $username ) ) ;
	$sql = "SELECT user_name FROM ".$cedar_user_table." WHERE ucase(user_name)=\"$tryusername\"";
	$res = $dbh->query( $sql ) ;
	if( !$res )
	{
	    $db_error = $dbh->lastError() ;
	    $wgOut->addHTML( "Failed to create user $username:<BR />\n" ) ;
	    $wgOut->addHTML( "Unable to query the database:<BR />\n" ) ;
	    $wgOut->addHTML( $db_error ) ;
	    $wgOut->addHTML( "<BR />\n" ) ;
	    return ;
	}

	if( $res->numRows() > 0 )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red\">The username $username already exists, please select a different one. If you need help logging in, go to our <A HREF=\"http://cedarweb.hao.ucar.edu/wiki/index.php/Help:Username_and_Password\">user help page</A></SPAN><BR /><BR />\n" ) ;
	    $this->createAccountForm() ;
	    return ;
	}

	$tryemail = $dbh->strencode( strtoupper( $email ) ) ;
	$sql = "SELECT user_name FROM ".$cedar_user_table." WHERE ucase(email)=\"$tryemail\"";
	$res = $dbh->query( $sql ) ;
	if( !$res )
	{
	    $db_error = $dbh->lastError() ;
	    $wgOut->addHTML( "Failed to create user $username:<BR />\n" ) ;
	    $wgOut->addHTML( "Unable to query the database:<BR />\n" ) ;
	    $wgOut->addHTML( $db_error ) ;
	    $wgOut->addHTML( "<BR />\n" ) ;
	    return ;
	}

	if( $upgrading == "false" && $res->numRows() > 0 )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red\">A user with email $email already exists. If you need help logging in, go to our <A HREF=\"http://cedarweb.hao.ucar.edu/wiki/index.php/Help:Username_and_Password\">user help page</A></SPAN><BR /><BR />\n" ) ;
	    $this->createAccountForm() ;
	    return ;
	}

	$username = $dbh->strencode( $username ) ;
	$sql = "SELECT user_name FROM ".$user_table." WHERE user_name=\"$username\"";
	$res = $dbh->query( $sql ) ;
	if( !$res )
	{
	    $db_error = $dbh->lastError() ;
	    $wgOut->addHTML( "Failed to create user $username:<BR />\n" ) ;
	    $wgOut->addHTML( "Unable to query the database:<BR />\n" ) ;
	    $wgOut->addHTML( $db_error ) ;
	    $wgOut->addHTML( "<BR />\n" ) ;
	    return ;
	}

	if( $upgrading == "false" && $res->numRows() > 0 )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red\">The username $username already exists, please select a different one. If you needhelp logging in, go to our <A HREF=\"http://cedarweb.hao.ucar.edu/wiki/index.php/Help:Username_and_Password\">user help page</A></SPAN><BR /><BR />\n" ) ;
	    $this->createAccountForm() ;
	    return ;
	}
	else if( $upgrading == "true" && $res->numRows() != 1 )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red\">The username $username does not exist</SPAN><BR />\n" ) ;
	    $this->createAccountForm() ;
	    return ;
	}

	$username = $dbh->strencode( $username ) ;
	$name = $dbh->strencode( $name ) ;
	$email = $dbh->strencode( $email ) ;
	$org = $dbh->strencode( $org ) ;
	$address1 = $dbh->strencode( $address1 ) ;
	$address2 = $dbh->strencode( $address2 ) ;
	$city = $dbh->strencode( $city ) ;
	$state = $dbh->strencode( $state ) ;
	$postal_code = $dbh->strencode( $postal_code ) ;
	$country = $dbh->strencode( $country ) ;
	$phone = $dbh->strencode( $phone ) ;
	$mobile_phone = $dbh->strencode( $mobile_phone ) ;
	$fax = $dbh->strencode( $fax ) ;
	$supervisor_name = $dbh->strencode( $supervisor_name ) ;
	$supervisor_email = $dbh->strencode( $supervisor_email ) ;
	$comments = $dbh->strencode( $comments ) ;
	if( $upgrading == "true" )
	{
	    $doupgrade = 1 ;
	}
	else
	{
	    $doupgrade = 0 ;
	}

	// generate a confirmation code to be emailed to the new user
	$dval = date( "YmdHis" ) ;
	$myrand = mt_rand( 100000000, 999999999 ) ;
	$code = "$dval-$myrand" ;

	// create entry in cedar_user_info table
	$insert_success = $dbh->insert( $cedar_user_table,
		array(
			'user_name' => $username,
			'real_name' => $name,
			'email' => $email,
			'organization' => $org,
			'address1' => $address1,
			'address2' => $address2,
			'city' => $city,
			'state' => $state,
			'postal_code' => $postal_code,
			'country' => $country,
			'phone' => $phone,
			'mobile_phone' => $mobile_phone,
			'fax' => $fax,
			'supervisor_name' => $supervisor_name,
			'supervisor_email' => $supervisor_email,
			'comments' => $comments,
			'upgrading' => $doupgrade,
			'status' => 'submitted',
			'code' => $code,
		),
		__METHOD__
	) ;

	if( $insert_success == false )
	{
	    $db_error = $dbh->lastError() ;
	    $wgOut->addHTML( "Failed to create user $username:<BR />\n" ) ;
	    $wgOut->addHTML( $db_error ) ;
	    $wgOut->addHTML( "<BR />\n" ) ;
	    return ;
	}

	require_once( 'UserMailer.php' ) ;
	$curr_date = date( "l, F d, Y h:i:s A" ) ;
	$from = new MailAddress( "cedar_db@hao.ucar.edu", "CEDAR DB" ) ;
	$to = new MailAddress( $email, $name ) ;
	$subject = "[CEDARWiki]: Access Confirmation" ;

	$body = "Below is the result of an account request form to CEDAR Database. Please click on the link to confirm your request\n" ;
	$confirm_url = "$wgServer/wiki/index.php/Special:Cedar_Create_Account?status=verify&wpName=$username&wpCode=$code" ;
	$body .= "$confirm_url\n\n" ;
	$body .= str_repeat( "-", 75 ) ;

	$result = UserMailer::send( $to, $from, $subject, $body );

	if( $result && !$result->isOK() )
	{
	    $wgOut->addHTML( "<SPAN STYLE=\"color:red;\">We were unable to send
	    your confirmation email.\n" ) ;
	    $wgOut->addHTML( "Please send an email to cedardb@hao.ucar.edu and provide them with the username that you entered.\n" ) ;
	    $wgOut->addHTML( "Also provide us with any error message that you see below. Thank you<BR /><BR /></SPAN>\n" ) ;
	    $msg = $result->getMessage() ;
	    $wgOut->addHTML( "$msg\n\n" ) ;
	    return ;
	}

	// redirect to submitted or just call from here
	$this->submitted() ;
    }

    function acceptNewAccount() {
	global $wgUser, $wgRequest, $wgOut, $wgAuth;
	global $wgServer;

	if ( wfReadOnly() ) {
		$wgOut->readOnlyPage();
		return false;
	}

	if (!$wgUser->isAllowedToCreateAccount()) {
		$this->userNotPrivilegedMessage();
		return false;
	}

	# account creation has been accepted. Need to grab the CedarUser
	# object for this username, create a new wiki user, grab that user's
	# id and put it in the CedarUser object and save the object, then
	# add the user to the groups list for group 'Cedar'
	$username = trim( $wgRequest->getVal('wpName') );
	$realname = trim( $wgRequest->getVal('wpRealName') );
	$email = trim( $wgRequest->getVal('wpEmail') );
	$upgrading = trim( $wgRequest->getVal('wpUpgrade') ) ;

	$wgOut->addHTML( "Getting cedar user info ... " ) ;
	$wgCedar = CedarUser::newFromName( $username ) ;
	if( is_null( $wgCedar ) ) {
	    $wgOut->addHTML( "FAILED - couldn't find cedar user info for $username" ) ;
	    return false ;
	}
	$id = $wgCedar->getId() ;
	if( $id )
	{
	    $wgOut->addHTML( "FAILED - id should not be set to $id" ) ;
	    return false ;
	}
	$wgOut->addHTML( "OK" ) ;
	$wgOut->addHTML( "<BR />" ) ;

	if( $upgrading == 'false' )
	{
	    $wgOut->addHTML( "Creating new user ... " ) ;
	    $u = User::newFromName( $username, 'creatable' );
	    if ( is_null( $u ) ) {
		$wgOut->addHTML( "FAILED" ) ;
		return false ;
	    }
	    if ( 0 != $u->idForName() ) {
		$wgOut->addHTML( "FAILED - user already exists" ) ;
		return false ;
	    }
	    if( !$wgAuth->addUser( $u, '' ) ) {
		$wgOut->addHTML( "FAILED - couldn't authorize new user" ) ;
		return false ;
	    }
	    $wgOut->addHTML( "OK" ) ;
	    $wgOut->addHTML( "<BR />" ) ;

	    $wgOut->addHTML( "Updating Site Stats ... " ) ;
	    # Update user count
	    $ssUpdate = new SiteStatsUpdate( 0, 0, 0, 0, 1 );
	    $ssUpdate->doUpdate();
	    $wgOut->addHTML( "OK" ) ;
	    $wgOut->addHTML( "<BR />" ) ;

	    $wgOut->addHTML( "Updating user info ... " ) ;
	    $u->addToDatabase();
	    $u->setPassword( null );
	    $u->setEmail( $email );
	    $u->setRealName( $realname );
	    $u->setToken();

	    $wgAuth->initUser( $u );

	    // Wipe the initial password and mail a temporary one
	    $u->setPassword( null );
	    $np = $u->randomPassword();
	    $u->setNewpassword( $np, false );
	    $u->saveSettings();
	    $wgOut->addHTML( "OK" ) ;
	    $wgOut->addHTML( "<BR />" ) ;
	} else {
	    $wgOut->addHTML( "Getting user ... " ) ;
	    $u = User::newFromName( $username, 'creatable' );
	    if ( is_null( $u ) ) {
		$wgOut->addHTML( "FAILED" ) ;
		return false ;
	    }
	    if ( 0 == $u->idForName() ) {
		$wgOut->addHTML( "FAILED - user doesn't exists" ) ;
		return false ;
	    }
	    $wgOut->addHTML( "OK" ) ;
	    $wgOut->addHTML( "<BR />" ) ;
	}
	$id = $u->getId() ;

	$wgOut->addHTML( "Updating cedar user info $id ... " ) ;
	$wgCedar->setId( $id ) ;
	$wgCedar->saveSettings() ;
	$wgOut->addHTML( "OK" ) ;
	$wgOut->addHTML( "<BR />" ) ;

	$wgOut->addHTML( "Updating CEDAR User Information... " ) ;
	$dbw =& wfGetDB( DB_MASTER );
	$update_success = $dbw->update( "cedar_user_info",
		array(
			'real_name' => "",
			'email' => "",
			'status' => "active"
		),
		array(
			'user_id' => $id
		),
		__METHOD__
	) ;

	if( $update_success == false )
	{
	    $db_error = $dbw->lastError() ;
	    $wgOut->addHTML( "Failed to update cedar user $username<BR />\n" ) ;
	    $wgOut->addHTML( $db_error ) ;
	}
	else
	{
	    $wgOut->addHTML( "OK" ) ;
	}
	$wgOut->addHTML( "<BR />" ) ;

	$wgOut->addHTML( "Adding group information (ignore errors/warnings)... " ) ;
	$group = "Cedar" ;
	$options = array( "IGNORE" ) ;
	$dbw->insert( 'user_groups',
		array(
			'ug_user' => $id,
			'ug_group' => $group,
		),
		__METHOD__,
		$options
	) ;
	$wgOut->addHTML( "OK" ) ;
	$wgOut->addHTML( "<BR />" ) ;

	$wgOut->addHTML( "Sending email to new user ... " ) ;
	if( $upgrading == 'false' )
	{
	    $m = "An account has been created successfully for you with username $username and temporary password $np on server $wgServer.\n\nBefore accessing data for the first time you must log in to the CEDAR wiki with this temporary password. Go to the CEDAR wiki at $wgServer, and click on 'Log in' in the upper right hand corner. Enter your username and this temporary password and click 'Log in'. You will be asked to create a new password. Once you have created your new, permanent password you will be able to access data from the CEDAR database at any time.\n\nWith this new account you will have access to the CedarWiki at $wgServer and the CEDAR database at http://www.vsto.org/\n\nFor information on how to access CEDAR data and examples please go to $wgServer/wiki/index.php/Data_Services:Examples";
	    $t = "[CEDAR] CEDAR database access granted and CedarWiki account created";
	} else {
	    $m = "Successfully upgraded account for $username. You now have permission to access the CEDAR database at $wgServer. as well as the CEDAR wiki.\n\nFor information on how to access CEDAR data and examples please go to $wgServer/wiki/index.php/Data_Services:Examples";
	    $t = "[CEDAR] CEDAR database access granted ";
	}
	$result = $u->sendMail( $t, $m );
	if( $result && !$result->isOK() )
	{
	    $wgOut->addHTML( "FAILED<BR />" ) ;
	    $wgOut->addWikiText( wfMsg( 'mailerror', $result->getMessage() ) ) ;
	}
	else
	{
	    $wgOut->addHTML( "OK<BR />" ) ;
	    $wgOut->addWikiText( wfMsg( 'accmailtext', $u->getName(), $u->getEmail() ) );
	}
	$wgOut->addHTML( "<BR />" ) ;

	$wgOut->addHTML( "Sending email confirmation to cedar_db ..." ) ;
	require_once( 'UserMailer.php' );
	$sender = new MailAddress( "cedar_db@hao.ucar.edu", "CEDAR DB" );
	$subject = "[CEDARWEB ACCESS FORM]: Access Granted for $realname with user name $username" ;
	$body = "CEDARWeb and CEDAR Wiki access has been granted to $realname with user name $username" ;
	$result = UserMailer::send( $sender, $sender, $subject, $body );

	if( $result && !$result->isOK() )
	{
	    $wgOut->addHTML( "FAILED<BR />" ) ;
	    $msg = $result->getMessage() ;
	    $wgOut->addHTML( "$msg\n" ) ;
	}
	else
	{
	    $wgOut->addHTML( "OK<BR />" ) ;
	}

	// there might be some internal hooks
	if( $upgrading == 'false' )
	{
	    wfRunHooks( 'AddNewAccount', array( $u ) );
	}

	$u = 0;
    }

    function denyNewAccount() {
	global $wgUser, $wgRequest, $wgOut;
	
	if ( wfReadOnly() ) {
		$wgOut->readOnlyPage();
		return false;
	}

	if (!$wgUser->isAllowedToCreateAccount()) {
		$this->userNotPrivilegedMessage();
		return false;
	}

	$dbw =& wfGetDB( DB_MASTER );

	# account creation has been denied, so delete the information given
	# the username from wpName
	$username = $dbw->strencode( $wgRequest->getVal('wpName') ) ;

	# from username delete the denied user
	$dbw->delete( 'cedar_user_info',
		array( /* SET */
			'user_name' => $username
		), __METHOD__
	);

	$wgOut->addWikiText( "=User $username has been denied a new account=" ) ; 

	$wgOut->addHTML( "Sending email confirmation to cedar_db ..." ) ;
	require_once( 'UserMailer.php' );
	$sender = new MailAddress( "cedar_db@hao.ucar.edu", "CEDAR DB" );
	$subject = "[CEDARWEB ACCESS FORM]: Access Denied for user $username" ;
	$body = "CEDARWeb and CEDARWiki access has been denied for user $username" ;
	$result = UserMailer::send( $sender, $sender, $subject, $body );

	if( $result && !$result->isOK() )
	{
	    $wgOut->addHTML( "FAILED<BR />" ) ;
	    $msg = $result->getMessage() ;
	    $wgOut->addHTML( "$msg\n" ) ;
	}
	else
	{
	    $wgOut->addHTML( "OK<BR />" ) ;
	}

    }

    function userNotPrivilegedMessage() {
	    global $wgOut;

	    $wgOut->setPageTitle( wfMsg( 'badaccess' ) );
	    $wgOut->setRobotpolicy( 'noindex,nofollow' );
	    $wgOut->setArticleRelated( false );

	    $wgOut->addWikiText( wfMsg( 'badaccess-group0' ) );

	    $wgOut->returnToMain( false );
    }
}

?>
