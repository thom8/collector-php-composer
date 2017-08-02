<?php

function path_join($base, $path) {
    return rtrim($base, '/') . '/' . ltrim($path, '/');
}

function schema_version($version) {
    // the currently installed version has an * in front
    if(substr( $version, 0, 2 ) === "* ") {
        $version = substr( $version, 2 );
    }

    // dependencies.io schema expects it in this form
    return array('version' => $version);
}

$collected = array();

$composer_json = json_decode(file_get_contents(path_join(path_join('/repo', $argv[1]), 'composer.json')), true);
$composer_require = array_key_exists('require', $composer_json) ? $composer_json['require'] : array();
$composer_require_dev = array_key_exists('require-dev', $composer_json) ? $composer_json['require-dev'] : array();

$composer_lock = json_decode(file_get_contents(path_join(path_join('/repo', $argv[1]), 'composer.lock')), true);
$composer_packages = array_key_exists('packages', $composer_lock) ? $composer_lock['packages'] : array();
$composer_packages_dev = array_key_exists('packages-dev', $composer_lock) ? $composer_lock['packages-dev'] : array();

$all_packages = array_merge($composer_packages, $composer_packages_dev);
$all_requirements = array_merge($composer_require, $composer_require_dev);

foreach ($all_requirements as $name => $spec) {
    echo "Collecting $name\n";

    $info_output = shell_exec("composer show $name --all");
    preg_match('/^versions : (.*)$/m', $info_output, $matches);

    if (count($matches) > 1) {
        $versions_string = $matches[1];
        $versions = explode(', ', $versions_string);
        $available = array_map(schema_version, $versions);
    } else {
        throw new Exception("No available versions found for \"$name\"");
    }

    echo "Finding installed version of \"$name\" based on composer.lock\n";

    $installed_version = array_filter($all_packages, function($package) use($name) {
        return strtolower($package['name']) == strtolower($name);
    });
    // indexes might be thrown off
    $installed_version = array_values($installed_version)[0]['version'];

    $schema_output = array(
        'name' => $name,
        'source' => 'packagist',  // TODO any way to tell if it came from another repository?
        'path' => $argv[1],
        'installed' => array('version' => $installed_version),
        'available' => $available
    );

    array_push($collected, $schema_output);
}

// send the final output to stdout so dependencies.io can pick it up
$final_output = json_encode(array('dependencies' => $collected));
echo('BEGIN_DEPENDENCIES_SCHEMA_OUTPUT>' . $final_output . '<END_DEPENDENCIES_SCHEMA_OUTPUT\n');
