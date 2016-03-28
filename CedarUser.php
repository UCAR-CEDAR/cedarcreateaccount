<?php
# CEDAR
#
/**
 * See user.txt
 *
 * @package MediaWiki
 */

/**
 *
 * Fields in the cedar_user_info table
 * user_info_id		uniq id for this table
 * user_id		id from user table
 * user_name		initial user_name set by the user when created cedar account
 * 			this value is removed when the user is added to user
 * real_name		initial real_name set by the user when created cedar account
 * 			this value is removed when the user is added to user
 * email		initial email address set by the user when created cedar
 * 			this value is removed when the user is added to user
 * organization		organization where user works/studies
 * address1		organization addres 1
 * address2		organization addres 2
 * city			organization city
 * state		organization state
 * country		organization country
 * postal_code		organization postal_code
 * phone		users contact phone
 * mobile_phone		users contact mobile phone
 * fax			users fax #
 * supervisor_name	if programmer or student, user's supervisor's name
 * supervisor_email	if programmer or student, user's supervisor's email
 * registration_date	date user was registered ... not editable
 * comments		comments used by cedar administrator ... not editable by user
 *
 * Once the account has been created within the wiki then the fields user_name,
 * real_name, email are not used. Kept for histerical reasons
 *
 * registration_date is not editable by the user
 *
 * comments are used by cedarweb administrators and is not editable by the user
 */
 
# Serialized record version
define( 'MW_CEDAR_USER_VERSION', 4 );

/**
 *
 * @package MediaWiki
 */
class CedarUser
{
    /**
     * List of member variables which are saved to the shared cache
     * (memcached).
     *
     * Any operation which changes the corresponding database fields must 
     * call a cache-clearing function.
     */
    static $mCacheVars = array(
	    # user table
	    'mId',
	    'mCedarId',
	    'mName',
	    'mOrg',
	    'mAddress1',
	    'mAddress2',
	    'mCity',
	    'mState',
	    'mCountry',
	    'mPostalCode',
	    'mPhone',
	    'mMobilePhone',
	    'mFax',
	    'mSupervisorName',
	    'mSupervisorEmail',
	    'mRegistrationDate',
    );

    /**
     * The variable declarations
     */
    var $mCedarId, $mId, $mName, $mOrg, $mAddress1,
	    $mAddress2, $mCity, $mState, $mCountry, $mPostalCode, $mPhone,
	    $mMobilePhone, $mFax, $mSupervisorName, $mSupervisorEmail,
	    $mRegistrationDate;

    /**
     * Whether the cache variables have been loaded
     */
    var $mDataLoaded;

    /**
     * Load the user table data for this object from the source given by mFrom
     */
    function load()
    {
	    if ( $this->mDataLoaded )
	    {
		    return;
	    }
	    wfProfileIn( __METHOD__ );

	    # Set it now to avoid infinite recursion in accessors
	    $this->mDataLoaded = true;

	    switch ( $this->mFrom ) {
		    case 'defaults':
			    $this->loadDefaults();
			    break;
		    case 'id':
			    $this->loadFromId();
			    break;
		    case 'name':
			    $this->loadFromName();
			    break;
		    default:
			    throw new MWException( "Unrecognised value for User->mFrom: \"{$this->mFrom}\"" );
	    }
	    wfProfileOut( __METHOD__ );
    }

    /**
     * Load user table data given mId
     * @return false if the ID does not exist, true otherwise
     * @private
     */
    function loadFromId()
    {
	    global $wgMemc;
	    if ( $this->mId == 0 ) {
		    $this->loadDefaults();
		    return false;
	    } 

	    # Try cache
	    $key = wfMemcKey( 'cedar_user', 'id', $this->mId );
	    $data = $wgMemc->get( $key );
	    
	    if ( !is_array( $data ) || $data['mVersion'] < MW_CEDAR_USER_VERSION ) {
		    # Object is expired, load from DB
		    $data = false;
	    }
	    
	    if ( !$data ) {
		    wfDebug( "Cache miss for user {$this->mId}\n" );
		    # Load from DB
		    if ( !$this->loadFromDatabase() ) {
			    # Can't load from ID, user is anonymous
			    return false;
		    }

		    # Save to cache
		    $data = array();
		    foreach ( self::$mCacheVars as $name ) {
			    $data[$name] = $this->$name;
		    }
		    $data['mVersion'] = MW_CEDAR_USER_VERSION;
		    $wgMemc->set( $key, $data );
	    } else {
		    wfDebug( "Got user {$this->mId} from cache\n" );
		    # Restore from cache
		    foreach ( self::$mCacheVars as $name ) {
			    $this->$name = $data[$name];
		    }
	    }
	    return true;
    }

    /**
     * Load from the wiki id
     */
    static function newFromId( $id )
    {
	    $u = new CedarUser;
	    $u->mId = $id;
	    $u->mCedarId = 0;
	    $u->mName = '';
	    $u->mFrom = 'id';
	    return $u;
    }

    /**
     * Load user table data given username
     * @return false if the name does not exist, true otherwise
     * @private
     */
    function loadFromName() {
	    global $wgMemc;
	    if ( $this->mName == '' ) {
		    $this->loadDefaults();
		    return false;
	    } 

	    # Try cache
	    $key = wfMemcKey( 'cedar_user', 'name', $this->mname );
	    $data = $wgMemc->get( $key );
	    
	    if ( !is_array( $data ) || $data['mVersion'] < MW_CEDAR_USER_VERSION ) {
		    # Object is expired, load from DB
		    $data = false;
	    }
	    
	    if ( !$data ) {
		    wfDebug( "Cache miss for user {$this->mName}\n" );
		    # Load from DB
		    if ( !$this->loadFromDatabase() ) {
			    # Can't load from ID, user is anonymous
			    return false;
		    }

		    # Save to cache
		    $data = array();
		    foreach ( self::$mCacheVars as $name ) {
			    $data[$name] = $this->$name;
		    }
		    $data['mVersion'] = MW_CEDAR_USER_VERSION;
		    $wgMemc->set( $key, $data );
	    } else {
		    wfDebug( "Got user {$this->mName} from cache\n" );
		    # Restore from cache
		    foreach ( self::$mCacheVars as $name ) {
			    $this->$name = $data[$name];
		    }
	    }
	    return true;
    }

    /**
     * Load from the username
     */
    static function newFromName( $name ) {
	    $u = new CedarUser;
	    $u->mName = $name;
	    $u->mId = 0;
	    $u->mCedarId = 0;
	    $u->mFrom = 'name';
	    return $u;
    }

    /**
     * Set cached properties to default. Note: this no longer clears 
     * uncached lazy-initialised properties. The constructor does that instead.
     *
     * @private
     */
    function loadDefaults( $name = false ) {
	    wfProfileIn( __METHOD__ );

	    $this->mCedarId = 0 ;
	    $this->mId = 0 ;
	    $this->mName = '' ;
	    $this->mOrg = '' ;
	    $this->mAddress1 = '' ;
	    $this->mAddress2 = '' ;
	    $this->mCity = '' ;
	    $this->mState = '' ;
	    $this->mCountry = '' ;
	    $this->mPostalCode = '' ;
	    $this->mPhone = '' ;
	    $this->mMobilePhone = '' ;
	    $this->mFax = '' ;
	    $this->mSupervisorName = '' ;
	    $this->mSupervisorEmail = '' ;
	    $this->mRegistrationDate = '0000-00-00 00:00:00' ;

	    wfProfileOut( __METHOD__ );
    }
    
    /**
     * Load user and user_group data from the database
     * $this->mId must be set, this is how the user is identified.
     * 
     * @return true if the user exists, false if the user is anonymous
     * @private
     */
    function loadFromDatabase() {
	    # Paranoia
	    $this->mId = intval( $this->mId );
	    $this->mCedarId = intval( $this->mCedarId );

	    /** Anonymous user */
	    if( !$this->mId ) {
		if( !$this->mCedarId ) {
		    if( $this->mName == '' ) {
			$this->loadDefaults();
			return false;
		    }
		}
	    }

	    $dbr =& wfGetDB( DB_MASTER );
	    if( $this->mId ) {
		$s = $dbr->selectRow( 'cedar_user_info', '*', array( 'user_id' => $this->mId ), __METHOD__ );
	    }
	    else if( $this->mCedarId ) {
		$s = $dbr->selectRow( 'cedar_user_info', '*', array( 'user_info_id' => $this->mCedarId ), __METHOD__ );
	    }
	    else if( $this->mName != '' ) {
		$s = $dbr->selectRow( 'cedar_user_info', '*', array( 'user_name' => $this->mName ), __METHOD__ );
	    }

	    if ( $s !== false ) {
		    # Initialise user table data
		    $this->mCedarId = $s->user_info_id ;
		    $this->mOrg = $s->organization ;
		    $this->mAddress1 = $s->address1 ;
		    $this->mAddress2 = $s->address2 ;
		    $this->mCity = $s->city ;
		    $this->mState = $s->state ;
		    $this->mCountry = $s->country ;
		    $this->mPostalCode = $s->postal_code ;
		    $this->mPhone = $s->phone ;
		    $this->mMobilePhone = $s->mobile_phone ;
		    $this->mFax = $s->fax ;
		    $this->mSupervisorName = $s->supervisor_name ;
		    $this->mSupervisorEmail = $s->supervisor_email ;
		    $this->mRegistrationDate = $s->registration_date ;
		    return true;
	    } else {
		    # Invalid user_id
		    $this->mId = 0;
		    $this->loadDefaults();
		    return false;
	    }
    }

    /**
     * Clear various cached data stored in this object. 
     * @param string $reloadFrom Reload user and user_groups table data from a 
     *   given source. May be "name", "id", "defaults", "session" or false for 
     *   no reload.
     */
    function clearInstanceCache( $reloadFrom = false ) {
	    if ( $reloadFrom ) {
		    $this->mDataLoaded = false;
		    $this->mFrom = $reloadFrom;
	    }
    }

    /**
     * Clear user data from memcached.
     * Use after applying fun updates to the database; caller's
     * responsibility to update user_touched if appropriate.
     *
     * Called implicitly from invalidateCache() and saveSettings().
     */
    private function clearSharedCache() {
	    if( $this->mId ) {
		    global $wgMemc;
		    $wgMemc->delete( wfMemcKey( 'cedar_user', 'id', $this->mId ) );
	    }
    }

    /**
     * Get the cedar id
     */
    function getCedarId() { 
	    $this->load();
	    return $this->mCedarId; 
    }

    /**
     * Get the user id
     */
    function getId() {
	$this->load();
	return $this->mId;
    }

    /**
     * Set the user_id
     */
    function setId( $id ) {
	$this->mId = $id ;
    }
	

    /**
     * Get the org
     */
    function getOrg() { 
	    $this->load();
	    return $this->mOrg; 
    }

    /**
     * Set the org
     */
    function setOrg( $v ) {
	    $this->mOrg = $v;
    }

    /**
     * Get address1
     */
    function getAddress1() { 
	    $this->load();
	    return $this->mAddress1; 
    }

    /**
     * Set address1
     */
    function setAddress1( $v ) {
	    $this->mAddress1 = $v;
    }

    /**
     * Get address2
     */
    function getAddress2() { 
	    $this->load();
	    return $this->mAddress2; 
    }

    /**
     * Set address2
     */
    function setAddress2( $v ) {
	    $this->mAddress2 = $v;
    }

    /**
     * Get city
     */
    function getCity() { 
	    $this->load();
	    return $this->mCity; 
    }

    /**
     * Set city
     */
    function setCity( $v ) {
	    $this->mCity = $v;
    }

    /**
     * Get state
     */
    function getState() { 
	    $this->load();
	    return $this->mState; 
    }

    /**
     * Set state
     */
    function setState( $v ) {
	    $this->mState = $v;
    }

    /**
     * Get country
     */
    function getCountry() { 
	    $this->load();
	    return $this->mCountry; 
    }

    /**
     * Set country
     */
    function setCountry( $v ) {
	    $this->mCountry = $v;
    }

    /**
     * Get postal_code
     */
    function getPostalCode() { 
	    $this->load();
	    return $this->mPostalCode; 
    }

    /**
     * Set postal_code
     */
    function setPostalCode( $v ) {
	    $this->mPostalCode = $v;
    }

    /**
     * Get phone
     */
    function getPhone() { 
	    $this->load();
	    return $this->mPhone; 
    }

    /**
     * Set phone
     */
    function setPhone( $v ) {
	    $this->mPhone = $v;
    }

    /**
     * Get mobile_phone
     */
    function getMobilePhone() { 
	    $this->load();
	    return $this->mMobilePhone; 
    }

    /**
     * Set mobile_phone
     */
    function setMobilePhone( $v ) {
	    $this->mMobilePhone = $v;
    }

    /**
     * Get fax
     */
    function getFax() { 
	    $this->load();
	    return $this->mFax; 
    }

    /**
     * Set fax
     */
    function setFax( $v ) {
	    $this->mFax = $v;
    }

    /**
     * Get supervisor_name
     */
    function getSupervisorName() { 
	    $this->load();
	    return $this->mSupervisorName; 
    }

    /**
     * Set supervisor_name
     */
    function setSupervisorName( $v ) {
	    $this->mSupervisorName = $v;
    }

    /**
     * Get supervisor_email
     */
    function getSupervisorEmail() { 
	    $this->load();
	    return $this->mSupervisorEmail; 
    }

    /**
     * Set supervisor_email
     */
    function setSupervisorEmail( $v ) {
	    $this->mSupervisorEmail = $v;
    }

    /**
     * Get registration_date
     */
    function getRegistrationDate() { 
	    $this->load();
	    return $this->mRegistrationDate; 
    }

    /**
     * Save object settings into database
     * @fixme Only rarely do all these fields need to be set!
     */
    function saveSettings()
    {
	    if ( wfReadOnly() ) {
		return;
	    }
	    if ( 0 == $this->mCedarId ) {
		return;
	    }
	    
	    $dbw =& wfGetDB( DB_MASTER );
	    $dbw->update( 'cedar_user_info',
		    array( /* SET */
			    'user_id' => $this->mId,
			    'organization' => $this->mOrg,
			    'address1' => $this->mAddress1,
			    'address2' => $this->mAddress2,
			    'city' => $this->mCity,
			    'state' => $this->mState,
			    'country' => $this->mCountry,
			    'postal_code' => $this->mPostalCode,
			    'phone' => $this->mPhone,
			    'mobile_phone' => $this->mMobilePhone,
			    'fax' => $this->mFax,
			    'supervisor_name' => $this->mSupervisorName,
			    'supervisor_email' => $this->mSupervisorEmail
		    ), array( /* WHERE */
			    'user_info_id' => $this->mCedarId
		    ), __METHOD__
	    );
	    $this->clearSharedCache();
    }

    /**
     * @deprecated
     */
    function setLoaded( $loaded ) {}

    static function initPreferences( $prefs, $request )
    {
	global $wgUser, $wgCedar ;

	$wgCedar = CedarUser::newFromId( $wgUser->getID() ) ;
	$wgCedar->load() ;
	if( $wgCedar )
	{
	    $org = $request->getVal( 'wpCedarOrg', 'VALUE_NOT_SET' ) ;
	    if( $org != 'VALUE_NOT_SET' )
	    {
		$wgCedar->setOrg(  $org ) ;
	    }
	    $a1 = $request->getVal( 'wpCedarAddress1', 'VALUE_NOT_SET' ) ;
	    if( $a1 != 'VALUE_NOT_SET' )
	    {
		$wgCedar->setAddress1( $a1 ) ;
	    }
	    $a2 = $request->getVal( 'wpCedarAddress2' ) ;
	    if( $a2 != 'VALUE_NOT_SET' )
	    {
		$wgCedar->setAddress2( $a2 ) ;
	    }
	    $city = $request->getVal( 'wpCedarCity', 'VALUE_NOT_SET' ) ;
	    if( $city != 'VALUE_NOT_SET' )
	    {
		$wgCedar->setCity( $city ) ;
	    }
	    $state = $request->getVal( 'wpCedarState', 'VALUE_NOT_SET' ) ;
	    if( $state != 'VALUE_NOT_SET' )
	    {
		$wgCedar->setState( $state ) ;
	    }
	    $country = $request->getVal( 'wpCedarCountry', 'VALUE_NOT_SET' ) ;
	    if( $country != 'VALUE_NOT_SET' )
	    {
		$wgCedar->setCountry( $country ) ;
	    }
	    $pc = $request->getVal( 'wpCedarPostalCode', 'VALUE_NOT_SET' ) ;
	    if( $pc != 'VALUE_NOT_SET' )
	    {
		$wgCedar->setPostalCode( $pc ) ;
	    }
	    $phone = $request->getVal( 'wpCedarPhone', 'VALUE_NOT_SET' ) ;
	    if( $phone != 'VALUE_NOT_SET' )
	    {
		$wgCedar->setPhone( $phone ) ;
	    }
	    $mobile = $request->getVal( 'wpCedarMobilePhone', 'VALUE_NOT_SET' ) ;
	    if( $mobile != 'VALUE_NOT_SET' )
	    {
		$wgCedar->setMobilePhone( $mobile ) ;
	    }
	    $fax = $request->getVal( 'wpCedarFax', 'VALUE_NOT_SET' ) ;
	    if( $fax != 'VALUE_NOT_SET' )
	    {
		$wgCedar->setFax( $fax ) ;
	    }
	    $sname = $request->getVal( 'wpCedarSupervisorName', 'VALUE_NOT_SET' ) ;
	    if( $sname != 'VALUE_NOT_SET' )
	    {
		$wgCedar->setSupervisorName( $sname ) ;
	    }
	    $semail = $request->getVal( 'wpCedarSupervisorEmail' ) ;
	    if( $semail != 'VALUE_NOT_SET' )
	    {
		$wgCedar->setSupervisorEmail( $semail ) ;
	    }
	}

	return true ;
    }

    static function renderPreferences( $form, $out )
    {
	global $wgCedar ;

	if( $wgCedar )
	{
	    if( $wgCedar->getCedarId() != 0 )
	    {
		$out->addHTML('<fieldset><legend>Cedar</legend><table>');
		$out->addHTML(
		    $form->addRow(
			'<label for="wpCedarOrg">Organization</label>',
			"<input type='text' name='wpCedarOrg' id='wpCedarOrg' value=\"{$wgCedar->getOrg()}\" size='25' />"
		    )
		);
		$out->addHTML(
		    $form->addRow(
			'<label for="wpCedarAddress1">Address 1</label>',
			"<input type='text' name='wpCedarAddress1' id='wpCedarAddress1' value=\"{$wgCedar->getAddress1()}\" size='25' />"
		    )
		);
		$out->addHTML(
		    $form->addRow(
			'<label for="wpCedarAddress2">Address 2</label>',
			"<input type='text' name='wpCedarAddress2' id='wpCedarAddress2' value=\"{$wgCedar->getAddress2()}\" size='25' />"
		    )
		);
		$out->addHTML(
		    $form->addRow(
			'<label for="wpCedarCity">City</label>',
			"<input type='text' name='wpCedarCity' id='wpCedarCity' value=\"{$wgCedar->getCity()}\" size='25' />"
		    )
		);
		$out->addHTML(
		    $form->addRow(
			'<label for="wpCedarState">State</label>',
			"<input type='text' name='wpCedarState' id='wpCedarState' value=\"{$wgCedar->getState()}\" size='25' />"
		    )
		);
		$out->addHTML(
		    $form->addRow(
			'<label for="wpCedarCountry">Country</label>',
			"<input type='text' name='wpCedarCountry' id='wpCedarCountry' value=\"{$wgCedar->getCountry()}\" size='25' />"
		    )
		);
		$out->addHTML(
		    $form->addRow(
			'<label for="wpCedarPostalCode">Postal Code</label>',
			"<input type='text' name='wpCedarPostalCode' id='wpCedarPostalCode' value=\"{$wgCedar->getPostalCode()}\" size='25' />"
		    )
		);
		$out->addHTML(
		    $form->addRow(
			'<label for="wpCedarPhone">Phone</label>',
			"<input type='text' name='wpCedarPhone' id='wpCedarPhone' value=\"{$wgCedar->getPhone()}\" size='25' />"
		    )
		);
		$out->addHTML(
		    $form->addRow(
			'<label for="wpCedarMobilePhone">Mobile Phone</label>',
			"<input type='text' name='wpCedarMobilePhone' id='wpCedarMobilePhone' value=\"{$wgCedar->getMobilePhone()}\" size='25' />"
		    )
		);
		$out->addHTML(
		    $form->addRow(
			'<label for="wpCedarFax">Fax</label>',
			"<input type='text' name='wpCedarFax' id='wpCedarFax' value=\"{$wgCedar->getFax()}\" size='25' />"
		    )
		);
		$out->addHTML(
		    $form->addRow(
			'<label for="wpCedarSupervisorName">Supervisor Name</label>',
			"<input type='text' name='wpCedarSupervisorName' id='wpCedarSupervisorName' value=\"{$wgCedar->getSupervisorName()}\" size='25' />"
		    )
		);
		$out->addHTML(
		    $form->addRow(
			'<label for="wpCedarSupervisorEmail">Supervisor Email</label>',
			"<input type='text' name='wpCedarSupervisorEmail' id='wpCedarSupervisorEmail' value=\"{$wgCedar->getSupervisorEmail()}\" size='25' />"
		    )
		);
		$out->addHTML(
		    $form->addRow(
			    'registration date:&nbsp;',
			    $wgCedar->getRegistrationDate()
		    )
		);
		$out->addHTML( '</table></fieldset>' );
	    }
	}

	return true ;
    }

    static function savePreferences( $form, $user, &$message )
    {
	global $wgCedar ;

	if( $wgCedar )
	{
	    $wgCedar->saveSettings() ;
	}

	return true ;
    }

    static function resetPreferences( $form, $user )
    {
	global $wgCedar ;

	$wgCedar = CedarUser::newFromId( $user->getID() ) ;
	$wgCedar->load() ;

	return true ;
    }
}
?>
