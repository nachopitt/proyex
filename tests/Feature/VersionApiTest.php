<?php

namespace Tests\Feature;

use Tests\TestCase;

class VersionApiTest extends TestCase
{
    public function test_version_endpoint_returns_success()
    {
        $response = $this->get('/api/version');

        $response->assertStatus(200);
    }

    public function test_version_endpoint_returns_json_with_required_fields()
    {
        $response = $this->get('/api/version');

        $response->assertJson([
            'version' => true,
            'environment' => true,
        ]);
        $response->assertJsonStructure(['version', 'environment']);
    }

    public function test_version_endpoint_does_not_require_authentication()
    {
        // This test ensures the endpoint is publicly accessible
        $response = $this->get('/api/version');

        $response->assertStatus(200);
        $this->assertNotNull($response['version']);
        $this->assertNotNull($response['environment']);
    }

    public function test_version_endpoint_returns_environment_value()
    {
        $response = $this->get('/api/version');

        // Should be one of: local, testing, staging, production, etc.
        $this->assertIsString($response['environment']);
        $this->assertNotEmpty($response['environment']);
    }

    public function test_version_endpoint_returns_version_value()
    {
        $response = $this->get('/api/version');

        // Should be either from env var or file, or 'unknown'
        $this->assertIsString($response['version']);
        $this->assertNotEmpty($response['version']);
    }

    public function test_version_endpoint_matches_application_config_values()
    {
        $response = $this->get('/api/version');

        $response->assertJsonPath('version', config('app.version'));
        $response->assertJsonPath('environment', config('app.env'));
    }
}
