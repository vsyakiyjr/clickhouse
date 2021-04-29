<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeployController extends Controller {
	public function deploy(Request $request) {
        $pushDetails = $request->get('push');
        $forceDeploy = $request->get('force', false);

        $testDomain = 'test.ikeamania.by';
        $prodDomain = 'ikeamania.by';

        $command = "/home/webserver/deploy.sh ";

        if(!$pushDetails) {
            if($forceDeploy) {
                return passthru($command . ' ' . $prodDomain);
            }

            return 'No push details provided.';
        }

        $deployDomain = $testDomain;

        // see details at https://confluence.atlassian.com/bitbucket/event-payloads-740262817.html#EventPayloads-Push
        foreach ($pushDetails['changes'] as $change) {
            if($change['old']['name'] == 'master') {
                $deployDomain = $prodDomain;
                break;
            }
        }

        return passthru($command . ' ' . $deployDomain);
	}
}
