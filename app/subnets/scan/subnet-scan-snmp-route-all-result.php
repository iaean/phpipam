<?php

/*
 * Discover newsubnetshosts with snmp
 *******************************/

/* functions */
require( dirname(__FILE__) . '/../../../functions/functions.php');

# initialize user object
$Database 	= new Database_PDO;
$User 		= new User ($Database);
$Admin	 	= new Admin ($Database, false);
$Subnets	= new Subnets ($Database);
$Sections	= new Sections ($Database);
$Addresses	= new Addresses ($Database);
$Tools		= new Tools ($Database);
$Result 	= new Result ();

# verify that user is logged in
$User->check_user_session();

# strip input tags
$_POST = $Admin->strip_input_tags($_POST);

# validate csrf cookie
$User->csrf_cookie ("validate", "scan", $_POST['csrf_cookie']) === false ? $Result->show("danger", _("Invalid CSRF cookie"), true) : "";
# section
$section = $Sections->fetch_section("id", $_POST['sectionId-0']);
if ($section===false)                                           { $Result->show("danger", _("Invalid section Id"), true, true, false, true); }

# scan disabled
if ($User->settings->enableSNMP!="1")                           { $Result->show("danger", _("SNMP module disbled"), true); }

# check section permissions
if($Sections->check_permission ($User->user, $_POST['sectionId']) != 3) { $Result->show("danger", _('You do not have permissions to add new subnet in this section')."!", true, true); }

# loop
foreach ($_POST as $k=>$p) {
    # explode
    $k = explode("-", $k);
    # numeric
    if (is_numeric($k[1])) {
        // output array
        $subnets_all[$k[1]][$k[0]]=$p;
    }
}

# sort by mask size
function cmp_subnets($a, $b) {
    if ($a['subnet_dec'] == $b['subnet_dec']) { return 0; }
    return ($a['subnet_dec'] < $b['subnet_dec']) ? -1 : 1;
}
usort($subnets_all, "cmp_subnets");


# recompute parents
foreach ($subnets_all as $k=>$s) {
    foreach ($subnets_all as $sb) {
        if ($sb['subnet_dec']!==$s['subnet_dec'] && $sb['mask']!==$s['mask']) {
            if ($Subnets->is_subnet_inside_subnet ($s['subnet'], $sb['subnet'])) {
                $subnets_all[$k]['master'] = $sb['subnet'];
            }
        }
    }
}

# import each
if (isset($subnets_all)) {
    foreach ($subnets_all as $s) {
        # set new POST
        $_POST = $s;
        # create csrf token
        $csrf = $User->csrf_cookie ("create", "subnet");
        # permissions
        $subnet['permissions'] = $section->permissions;
        # check for master
        if (isset($s['master'])) {
            // find id
            $master = $Subnets->find_subnet ($s['sectionId'], $s['master']);
            if ($master!==false) {
                $_POST['masterSubnetId'] = $master->id;
            }
        }
        # include edit script
        include (dirname(__FILE__)."/../../admin/subnets/edit-result.php");
    }
}
else { $Result->show("danger", "No subnets selected", true); }

?>