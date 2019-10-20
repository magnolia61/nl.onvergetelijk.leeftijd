<?php

#error_reporting(E_ALL);
#ini_set('display_errors', TRUE);
#ini_set('display_startup_errors', TRUE);

require_once 'kampleeftijd.civix.php';

/**
 * Implementation of hook_civicrm_custom
 *
 * This is needed only if there is a computed (View Only) custom field in this set.
 */

function kampleeftijd_civicrm_validateprofile($profileName)
{
    $processkampleeftijd = 0;
    if ($profileName === 'BEHEER_GEBOORTEDATUM_101' or
        $profileName === 'Verjaardag_en_geslacht_68' or
        $profileName === 'Verjaardag_en_geslacht_97' or
        $profileName === 'Verjaardag_en_geslacht_66' or
        $profileName === 'Verjaardag_en_geslacht_67' or
        $profileName === 'Verjaardag_en_geslacht_99' or
        $profileName === 'Verjaardag_en_geslacht_19'
    ) {
        watchdog('php', '<pre>---STARTKAMPLEEFTIJD---</pre>', null, WATCHDOG_DEBUG);
        $processkampleeftijd = 1;
        watchdog('php', '<pre>validateprofile: profile_name:' . print_r($profileName, true) . '</pre>', null, WATCHDOG_DEBUG);
        watchdog('php', '<pre>set_processkampleeftijd:' . print_r($processkampleeftijd, true) . '</pre>', null, WATCHDOG_DEBUG);
        #watchdog('php', '<pre>gid:' . print_r($gid, true) . '</pre>', null, WATCHDOG_DEBUG);
        #watchdog('php', '<pre>id:' . print_r($id, true) . '</pre>', null, WATCHDOG_DEBUG);
        #watchdog('php', '<pre>group_id:' . print_r($groupID, true) . '</pre>', null, WATCHDOG_DEBUG);
        #watchdog('php', '<pre>entityid:' . print_r($entityID, true) . '</pre>', null, WATCHDOG_DEBUG);
        watchdog('php', '<pre>---ENDKAMPLEEFTIJD---</pre>', null, WATCHDOG_DEBUG);
    }
}

function kampleeftijd_civicrm_custom($op, $groupID, $entityID)
{

    #if (!in_array($groupID, array("103","181", "139", "190"))) { // ALLEEN PART + EVENT PROFILES
    if (!in_array($groupID, array("103","106","139"))) { // ALLEEN PART PROFILES
        // 101  EVENT KENMERKEN
        // 103  TAB  CURRICULUM
        // 106  TAB  WERVING
        // 139  PART DEEL
        // 190  PART LEID
        // (140 PART LEID VOG)
        // 181  TAB  INTAKE
        // 165  PART REFERENTIE
        // 205  PART 
        #if ($extdebug == 1) { watchdog('php', '<pre>--- SKIP EXTENSION CV (not in proper group) [groupID: '.$groupID.'] [op: '.$op.']---</pre>', null, WATCHDOG_DEBUG); }
        return; //   if not, get out of here
    }

    watchdog('php', '<pre>---STARTKAMPLEEFTIJD_custom---</pre>', null, WATCHDOG_DEBUG);
    #watchdog('php', '<pre>' . print_r($op, true) . '</pre>', null, WATCHDOG_DEBUG);
    #watchdog('php', '<pre>' . print_r($groupID, true) . '</pre>', null, WATCHDOG_DEBUG);
    #watchdog('php', '<pre>'. print_r($entityID, TRUE) .'</pre>', NULL, WATCHDOG_DEBUG);
    if ($op != 'create' && $op != 'edit') {
        $processkampleeftijd = 0;
        //    did we just create or edit a custom object?
        #watchdog('php', '<pre>return:' . print_r($op, true) . '</pre>', null, WATCHDOG_DEBUG);
        return; //    if not, get out of here
    } else if ($groupID == 106) {
        //  group ID of the Werving Custom Field set (106 = WERVING, profile leeftijd = 68, 97, 66, 67, 99, 19, 213 )
        $processkampleeftijd = 1;
        #watchdog('php', '<pre>civicrm_custom:' . print_r($groupID, true) . '</pre>', null, WATCHDOG_DEBUG);
        #watchdog('php', '<pre>set_processkampleeftijd:' . print_r($processkampleeftijd, true) . '</pre>', null, WATCHDOG_DEBUG);
        $result = kampleeftijd_configure($op, $groupID, $entityID);
    }
    watchdog('php', '<pre>value_processkampleeftijd:' . print_r($processkampleeftijd, true) . '</pre>', null, WATCHDOG_DEBUG);
    watchdog('php', '<pre>---ENDKAMPLEEFTIJD_custom---</pre>', null, WATCHDOG_DEBUG);
}

function kampleeftijd_configure($op, $groupID, $entityID)
{

    watchdog('php', '<pre>---STARTKAMPLEEFTIJD PROCESS---</pre>', null, WATCHDOG_DEBUG);
    $tableName1      = "civicrm_value_werving_promotie_106"; //    table name for the custom group (each set of custom fields has a corresponding table in the database)
    $tableName2      = "civicrm_contact"; //    table name for the custom group (each set of custom fields has a corresponding table in the database)
    $datumkomendkamp = '2019-08-01';
    $sql1            = "SELECT ROUND(TIMESTAMPDIFF(MONTH,C.birth_date, '2020-08-01')/12,1) AS leeftijdkomendkamp, C.id FROM $tableName2 AS C WHERE C.id = $entityID";
    #watchdog('php', '<pre>' . print_r($sql1, true) . '</pre>', null, WATCHDOG_DEBUG);
    $dao1 = CRM_Core_DAO::executeQuery($sql1, CRM_Core_DAO::$_nullArray);
    /* watchdog('php', '<pre>'. print_r($dao1, TRUE) .'</pre>', NULL, WATCHDOG_DEBUG); */

    while ($dao1->fetch()) {
        $id                       = $dao1->id;
        $leeftijd_komendkamp      = 0;
        $leeftijd_komendkamp_rond = 0;
        $leeftijd_komendkamp      = $dao1->leeftijdkomendkamp;
        $leeftijd_komendkamp_rond = floor($dao1->leeftijdkomendkamp);
        watchdog('php', '<pre>leeftijdkomendkamp:'. print_r($leeftijd_komendkamp, TRUE) .'</pre>', NULL, WATCHDOG_DEBUG);
        watchdog('php', '<pre>leeftijdkomendkamp_rond:'. print_r($leeftijd_komendkamp_rond, TRUE) .'</pre>', NULL, WATCHDOG_DEBUG);

        if (!empty($leeftijd_komendkamp)) {
            /* $sql2 = "UPDATE $tableName1 SET leeftijd_komendkamp_552 = $leeftijd_komendkamp WHERE entity_id = $id"; */
            $sql2 = "UPDATE $tableName1 SET leeftijd_komendkampgetal_571 = $leeftijd_komendkamp, leeftijd_komendkamprond_871 = $leeftijd_komendkamp_rond WHERE entity_id = $id";
            watchdog('php', '<pre>' . print_r($sql2, true) . '</pre>', null, WATCHDOG_DEBUG);
            $dao2 = CRM_Core_DAO::executeQuery($sql2, CRM_Core_DAO::$_nullArray);
            /* watchdog('php', '<pre>'. print_r($dao2, TRUE) .'</pre>', NULL, WATCHDOG_DEBUG); */

            if ($leeftijd_komendkamp_rond < 18) {
                watchdog('php', '<pre>leeftijdkomendkamp [emailgreeting2]:' . print_r($leeftijd_komendkamp, true) . '</pre>', null, WATCHDOG_DEBUG);
                $result = civicrm_api3('Contact', 'create', array(
                    'debug'                   => 1,
                    'email_greeting_display'  => 2,
                    'email_greeting_id'       => "Dear (parent of) {contact.first_name}",
                    'postal_greeting_display' => 2,
                    'postal_greeting_id'      => "Dear (parent of) {contact.first_name}",
                    'id'                      => $id,
                ));
            } else {
                watchdog('php', '<pre>leeftijdkomendkamp [emailgreeting1]:' . print_r($leeftijd_komendkamp, true) . '</pre>', null, WATCHDOG_DEBUG);
                $result = civicrm_api3('Contact', 'create', array(
                    'debug'                   => 1,
                    'email_greeting_display'  => 1,
                    'email_greeting_id'       => "Dear {contact.first_name}",
                    'postal_greeting_display' => 1,
                    'postal_greeting_id'      => "Dear {contact.first_name}",
                    'id'                      => $id,
                ));
            }
        }
    }
    watchdog('php', '<pre>---ENDKAMPLEEFTIJD PROCESS---</pre>', null, WATCHDOG_DEBUG);
}

/**
 * Implementation of hook_civicrm_config
 */
function kampleeftijd_civicrm_config(&$config)
{
    _kampleeftijd_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function kampleeftijd_civicrm_xmlMenu(&$files)
{
    _kampleeftijd_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function kampleeftijd_civicrm_install()
{
    return _kampleeftijd_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function kampleeftijd_civicrm_uninstall()
{
    return _kampleeftijd_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function kampleeftijd_civicrm_enable()
{
    return _kampleeftijd_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function kampleeftijd_civicrm_disable()
{
    return _kampleeftijd_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function kampleeftijd_civicrm_managed(&$entities)
{
    return _kampleeftijd_civix_civicrm_managed($entities);
}
