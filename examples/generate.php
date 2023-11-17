<?php

$perm = [];
$role = [];
$group = [];

$modules = ['Compta', 'Benef', 'Product', 'Commande'];

foreach ($modules as $module) {
	for ($i = 1; $i < 50; $i++) {
        $permModule = 'perm'.$module;
        $data = 'data'.$module.$i;

		$perm[] = ['p', $permModule.'ReadData'.$i, '*', $data, 'read'];
		$perm[] = ['p', $permModule.'WriteData'.$i, '*', $data, 'write'];
		$perm[] = ['p', $permModule.'ExportData'.$i, '*', $data, 'export'];

		$role[] = ['g', 'adminrole'.$module, $permModule.'ReadData'.$i, '*'];
		$role[] = ['g', 'adminrole'.$module, $permModule.'WriteData'.$i, '*'];
		$role[] = ['g', 'adminrole'.$module, $permModule.'ExportData'.$i, '*'];

		$role[] = ['g', 'readerrole'.$module, $permModule.'ReadData'.$i, '*'];
	}

	$group[] = ['g', 'admingroup', 'adminrole'.$module, '*'];
	$group[] = ['g', 'readergroup', 'readerrole'.$module, '*'];
}

$user = [];

$nbDomain = 300;
for ($idDomain = 1; $idDomain <= $nbDomain; $idDomain ++) {
    $nbUserByDomain = random_int(50, 800);

    $domainGroup = 'customgroupDomain'.$idDomain;
    $group[] = ['g', $domainGroup, 'adminroleCompta', 'domain'.$idDomain];
    $group[] = ['g', $domainGroup, 'adminroleBenef', 'domain'.$idDomain];
    $group[] = ['g', $domainGroup, 'readerroleProduct', 'domain'.$idDomain];

	for ($idUser = 1; $idUser <= $nbUserByDomain; $idUser ++) {

		$userGroup = 'readergroup';
		if ($idUser <= 10) {
			$userGroup = 'admingroup';
            $user[] = ['g', 'user-'.$idDomain.'-'.$idUser, 'readergroup', 'domain'.($idDomain + 1)];
		}
        elseif ($idUser === 11) {
            $userGroup = 'adminroleCompta';
        } elseif ($idUser === 12) {
            $userGroup = 'adminroleBenef';
        } elseif ($idUser === 13) {
            $userGroup = 'adminroleProduct';
        } elseif ($idUser === 14) {
            $userGroup = 'adminroleCommand';
        }
        elseif ($idUser === 15) {
            $userGroup = 'readerroleCompta';
        } elseif ($idUser === 16) {
            $userGroup = 'readerroleBenef';
        } elseif ($idUser === 17) {
            $userGroup = 'readerroleProduct';
        } elseif ($idUser === 18) {
            $userGroup = 'readerroleCommand';
        }
        elseif ($idUser === 20) {
            $userGroup = $domainGroup;
        }

		$user[] = ['g', 'user-'.$idDomain.'-'.$idUser, $userGroup, 'domain'.$idDomain];
	}
}

$tableName = 'casbin_rule';

$user[] = ['g', 'user-all-1', 'admingroup', '*'];
$user[] = ['g', 'user-all-2', 'readergroup', '*'];

echo "\nINSERT INTO ".$tableName." (p_type, v0, v1, v2, v3, v4, v5) VALUES \n";
$sep = '';
foreach ($perm as $p) {
    $sep = (next($perm)) ? ',' : ';';
	echo sprintf("('%s', '%s', '%s', '%s', '%s', '', '')%s\n", $p[0], $p[1], $p[2], $p[3], $p[4], $sep);
}

echo "\nINSERT INTO ".$tableName." (p_type, v0, v1, v2, v3, v4, v5) VALUES \n";
$sep = '';
foreach ($role as $r) {
    $sep = (next($role)) ? ',' : ';';
	echo sprintf("('%s', '%s', '%s', '%s', '', '', '')%s\n", $r[0], $r[1], $r[2], $r[3], $sep);
}

echo "\nINSERT INTO ".$tableName." (p_type, v0, v1, v2, v3, v4, v5) VALUES \n";
$sep = '';
foreach ($group as $r) {
    $sep = (next($group)) ? ',' : ';';
	echo sprintf("('%s', '%s', '%s', '%s', '', '', '')%s\n", $r[0], $r[1], $r[2], $r[3], $sep);
}

echo "\nINSERT INTO ".$tableName." (p_type, v0, v1, v2, v3, v4, v5) VALUES \n";
$sep = '';
foreach ($user as $r) {
    $sep = (next($user)) ? ',' : ';';
	echo sprintf("('%s', '%s', '%s', '%s', '', '', '')%s\n", $r[0], $r[1], $r[2], $r[3], $sep);
}