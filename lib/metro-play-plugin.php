<?php

class Metro_Play_Plugin {

	private $plugin_path;

	public function __construct( $plugin_path ) {
		$this->plugin_path = $plugin_path;
		$this->add_actions();
	}

	private function add_actions() {
		add_action( 'init', array( &$this, 'add_post_type' ) );

		add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( &$this, 'save_postdata' ) );

		add_filter( 'manage_edit-games_columns', array( &$this, 'add_column' ) );
		add_action( 'manage_games_posts_custom_column', array( &$this, 'custom_column' ), 10, 2 );
		add_filter( 'manage_edit-games_sortable_columns', array( &$this, 'sortable_columns' ) );

	}

	public function add_post_type() {
		register_post_type(
			'games',
			array(
				'labels' => array(
					'name' => __( 'Games' ),
					'singular_name' => __( 'Game' ),
				),
				'public' => true,
			)
		);
	}

	function add_meta_boxes() {
		add_meta_box(
			'game_id',
			__( 'Game ID' ),
			array( &$this, 'game_id_inner_custom_box' ),
			'games'
		);

		add_meta_box(
			'game_type',
			__( 'Game Type' ),
			array( &$this, 'game_type_inner_custom_box' ),
			'games'
		);
	}


	public function game_id_inner_custom_box( $post ) {
		wp_nonce_field( plugin_basename( $this->plugin_path ), 'metro_nonce_game_id' );
		$value = get_post_meta( $post->ID, 'metro_game_id', TRUE );

		echo '<input type="text" id="metro_game_id" name="metro_game_id" value="' . esc_attr( $value ) . '" />';
	}

	public function game_type_inner_custom_box( $post ) {
		wp_nonce_field( plugin_basename( $this->plugin_path ), 'metro_nonce_game_type' );
		$value = get_post_meta( $post->ID, 'metro_game_type', TRUE );

		echo '<select id="metro_game_type" name="metro_game_type" >';
		echo '<option>Slots</option>';
		echo '<option value="slots_10"' . selected( $value, 'slots_10' ) . '>- 10 lines</option>';
		echo '<option value="slots_20"' . selected( $value, 'slots_20' ) . '>- 20 lines</option>';
		echo '<option>Tables</option>';
		echo '<option value="blackjack"' . selected( $value, 'blackjack' ) . '>- Blackjack</option>';
		echo '<option value="roulette"' . selected( $value, 'roulette' ) . '>- Roulette</option>';
		echo '<option value="other"' . selected( $value, 'other' ) . '>Other Games</option>';
		echo '</select>';
	}

	public function save_postdata( $post_id ) {
		if ( ! isset( $_POST['metro_nonce_game_id'] ) || ! wp_verify_nonce( $_POST['metro_nonce_game_id'], plugin_basename( $this->plugin_path ) ) ) {
			return;
		}

		if ( ! isset( $_POST['metro_nonce_game_type'] ) || ! wp_verify_nonce( $_POST['metro_nonce_game_type'], plugin_basename( $this->plugin_path ) ) ) {
			return;
		}

		$post_ID = $_POST['post_ID'];
		$game_id = sanitize_text_field( $_POST['metro_game_id'] );
		$game_type = sanitize_text_field( $_POST['metro_game_type'] );

		if ( !add_post_meta( $post_ID, 'metro_game_id', $game_id, TRUE ) ) {
			update_post_meta( $post_ID, 'metro_game_id', $game_id );
		}

		if ( !add_post_meta( $post_ID, 'metro_game_type', $game_type, TRUE ) ) {
			update_post_meta( $post_ID, 'metro_game_type', $game_type );
		}
	}

	public function custom_column( $column, $post_id ){
		switch( $column ) {
			case 'game_id' :
				$game_id = get_post_meta( $post_id, 'metro_game_id', TRUE );

				if ( empty( $game_id ) ) {
					echo __( 'Unknown' );
				} else {
					printf( __( '%s' ), $game_id );
				}

				break;
			default :
				break;
		}
	}

	function add_column( $columns, $post_id ) {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Title' ),
			'game_id' => __( 'Game ID' ),
			'date' => __( 'Date' )
		);

		return $columns;
	}

	function sortable_columns( $columns ) {
		$columns['game_id'] = 'game_id';

		return $columns;
	}

}