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

// get list of everything installed currently
$show_output = shell_exec('composer show --latest --format=json');
$installed = json_decode($show_output, true);

$composer_json = json_decode(file_get_contents(path_join(path_join('/repo', $argv[1]), 'composer.json')), true);
$composer_require = array_key_exists('require', $composer_json) ? $composer_json['require'] : array();
$composer_require_dev = array_key_exists('require-dev', $composer_json) ? $composer_json['require-dev'] : array();

foreach ($installed['installed'] as $package) {
    $name = $package['name'];

    // we only want direct dependencies, not their dependencies
    if (!array_key_exists($name, $composer_require) && !array_key_exists($name, $composer_require_dev)) {
        continue;
    }

    echo "Collecting $name";

    $installed_version = $package['version'];

    $info_output = shell_exec("composer show $name --all");
    preg_match('/^versions : (.*)$/m', $info_output, $matches);

    if (count($matches) > 1) {
        $versions_string = $matches[1];
        $versions = explode(', ', $versions_string);
        $available = array_map(schema_version, $versions);
    } else {
        $available = array();
    }

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
