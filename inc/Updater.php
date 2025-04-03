<?php
/**
 * Github Updater
 *
 * PHP Version 8.2
 *
 * @package featured-image-block-fallback
 * @author  Bob Moore <bob@bobmoore.dev>
 * @license GPL-2.0+ <http://www.gnu.org/licenses/gpl-2.0.txt>
 * @link    https://github.com/bob-moore/Featured-Image-Block-Fallback
 * @since   0.1.0
 */

 namespace Rcm\SimpleMenuBlock;

class Updater
{
    /**
     * The plugin slug.
     *
     * @var string
     */
    const SLUG = 'featured-image-block-fallback';
    /**
     * The root directory of the plugin.
     *
     * @var string
     */
    protected string $plugin_root;
    /**
     * The plugin directory.
     *
     * @var string
     */
    protected string $dir;
    /**
     * The plugin URL.
     *
     * @var string
     */
    protected string $url;
    /**
     * The plugin version.
     *
     * @var string
     */
    protected string $plugin_version;
    /**
     * The plugin icon.
     *
     * @var string
     */
    protected string $plugin_icon;
    /**
     * The plugin banner small.
     *
     * @var string
     */
    protected string $plugin_banner_small;
    /**
     * The plugin banner large.
     *
     * @var string
     */
    protected string $plugin_banner_large;
    /**
     * The api slug to check for updates.
     *
     * @var string
     */
    protected string $api_slug;

    public function __construct( string $root_file )
    {
        $this->plugin_root = dirname( $root_file );

        do_action( 'qm/debug', $this->plugin_root );

        // add_action( 'init', [ $this, 'init' ] );
        // add_filter( 'plugins_api', [ $this, 'info' ], 20, 3 );
        // add_filter( 'site_transient_update_plugins', [ $this, 'update' ] );
    }

    public function init(): void
    {
        // $plugin_data = get_file_data(
        //     $this->root_file,
        //     [
        //         'PluginURI' => 'Plugin URI',
        //         'Version' => 'Version',
        //         'TestedUpTo' => 'Tested up to',
        //         'UpdateURI' => 'Update URI',
        //         'GithubURI' => 'Github URI',
        //     ]
        // );

        // $this->dir = str_replace( ABSPATH, '', dirname( $this->root_file ) );
        // $this->url = trailingslashit( trailingslashit( WP_SITEURL ) . $this->dir);
        // $this->plugin_version = $plugin_data['Version'];
    }
    protected function setUrl(): void
    {
        if ( defined( 'WP_SITEURL' ) ) {
            $this->url = trailingslashit( trailingslashit( WP_SITEURL ) . $this->dir);
        }
    }
    /**
     * Helper function to create URLs.
     *
     * @param string $path : The path to append to the URL.
     *
     * @return string
     */
    protected function url( string $path ): string
    {
        return $this->url . ltrim( $path, '/' );
    }
    /**
     * Helper function to create paths.
     *
     * @param string $path : The path to append to the directory.
     *
     * @return string
     */
    protected function path( string $path ): string
    {
        return $this->dir . ltrim( $path, '/' );
    }

    /**
     * Filters the plugins_api() response.
     *
     * @param false|object|array $result The result object or array. Default false.
     * @param string             $action The type of information being requested from the Plugin Installation API.
     * @param object             $args Plugin API arguments.
     *
     * @return false|object|array
     */
    public function info( false|object|array $result, string $action, object $args ): false|object|array
    {
        
        if ( 'plugin_information' !== $action ) {
            return $result;
        }

        if ( empty( $args->slug ) || self::SLUG !== $args->slug ) {
            return $result;
        }

        $data = $this->request();

        if ( ! $data ) {
            return $result;
        }

        $response          = new \stdClass();
        $response->slug    = self::SLUG;
        $response->plugin  = self::SLUG . '/' . self::SLUG . '.php';
        $response->version = $data->new_version;
        $response->name    = $data->name;
        $response->banners = [
            'low' => $this->url( 'assets/banner-772x250.jpg' ),
            'high' => $this->url( 'assets/banner-1544x500.jpg' ),
        ];
        $response->sections = (array) $data->sections;

        return $response;
    }
    /**
     * Request the update information from the API.
     *
     * @return object
     */
    protected function request(): ?object
    {
        $remote = wp_remote_get("https://raw.githubusercontent.com/bob-moore/Featured-Image-Block-Fallback/main/manifest.json");

        if ( is_wp_error( $remote ) 
            || 200 !== wp_remote_retrieve_response_code( $remote )
        ) {
            return null;
        }
        $body = wp_remote_retrieve_body( $remote );

        return json_decode( $body );
    }
    /**
     * Filters the update transient.
     *
     * @param object $transient The transient object.
     *
     * @return object
     */
    public function update( $transient ) {

        if ( empty( $transient->checked ) ) {
            return $transient;
        }
        
        $manifest = $this->request();

        if ( ! $manifest ) {
            return $transient;
        }

        if ( 
            ! version_compare( $this->plugin_version, $manifest->new_version, '<' )
            || ! version_compare( $manifest->requires, get_bloginfo( 'version' ), '<=' )
            || ! version_compare( $manifest->requires_php, PHP_VERSION, '<' )
        ) {
            return $transient;
        }

        $response               = new \stdClass();
        $response->slug         = 'featured-image-block-fallback';
        $response->plugin       = self::SLUG . '/' . self::SLUG . '.php';
        $response->new_version  = $manifest->new_version;
        $response->tested       = $manifest->tested;
        $response->requires     = $manifest->requires;
        $response->requires_php = $manifest->requires_php;
        $response->package      = $manifest->package;
        $response->added        = $manifest->added;
        $response->last_updated = $manifest->last_updated;
        $response->icons = [
            'default' => $this->url( 'assets/icon.png' ),
        ];

        $transient->response[ $response->plugin ] = $response;

        return $transient;
    }
}