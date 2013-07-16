<?php
if ( !class_exists('STF_Widget') )
{
	class STF_Widget extends WP_Widget {

		public function __construct() {
			parent::__construct(
				'stf_widget',
				'SimpleTwit Widget',
				array(
					'classname' => 'stf_widget',
					'description' => 'Displays your most recent Tweets'
				));
		}

		public function widget( $args, $instance ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
			$num = is_numeric($instance['num']) ? $instance['num'] : 5;
			$show_content = is_bool($instance['show_content']) ? $instance['show_content'] : true;
			$show_time = is_bool($instance['show_time']) ? $instance['show_time'] : true;
			$show_author = is_bool($instance['show_author']) ? $instance['show_author'] : true;
			$show_source = is_bool($instance['show_source']) ? $instance['show_source'] : false;

			echo $args['before_widget'];
			if ( !empty( $title ) )
				echo $args['before_title'] . $title . $args['after_title'];

			$tweets = stf_get_tweets(array( 'num' => $instance['num'] ));
			if (!empty($tweets)) :
				?>
			<div class="stf-tweets">
				<?php
				foreach ($tweets as $tweet) :
					$classes = array('tweet');
					if ($tweet->is_reply) $classes[] = 'reply';
					if ($tweet->is_retweet) $classes[] = 'retweet';
				?>
				<article class="<?php echo implode(' ', $classes); ?>">
					<?php if ($show_content) : ?>
					<p class="tweet-content">
						<?php echo $tweet->content; ?>
					</p>
					<?php endif; ?>
					<?php if ($show_time) : ?>
					<time datetime="<?php echo $tweet->time_gmt; ?>" class="tweet-time"><a href="<?php echo $tweet->get_link(); ?>"><?php echo $tweet->time_str; ?></a></time>
					<?php endif; ?>
					<?php if ($show_author) :
						$author = $tweet->get_author();
						?>
					<a href="<?php echo $tweet->get_author_link(); ?>" class="tweet-author">@<?php echo $author->screen_name; ?></a>
					<?php endif; ?>
					<?php if ($show_source) : ?>
					<p class="tweet-source">
						Sent from <?php echo $tweet->get_source(); ?>
					</p>
					<?php endif; ?>
				</article>
				<?php
				endforeach;
				?>
			</div>
				<?php
			else :
				?>
			<p class="no-tweets">There are no Tweets to display.</p>
				<?php
			endif;

			echo $args['after_widget'];
		}

		public function form( $instance ) {
			$title = isset($instance['title']) ? $instance['title'] : 'Recent Tweets';
			$num = isset($instance['num']) ? $instance['num'] : 5;
			$show_content = isset($instance['show_content']) ? $instance['show_content'] : true;
			$show_time = isset($instance['show_time']) ? $instance['show_time'] : true;
			$show_author = isset($instance['show_author']) ? $instance['show_author'] : true;
			$show_source = isset($instance['show_source']) ? $instance['show_source'] : false;
			?>
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo 'Title:'; ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'num' ); ?>"><?php echo 'Number of tweets to display:'; ?></label>
				<input name="<?php echo $this->get_field_name( 'num' ); ?>" id="<?php echo $this->get_field_id( 'num' ); ?>" type="text" value="<?php echo esc_attr( $num ); ?>" size="3">
			</p>
			<p>
				Show:<br>
				<div class="alignleft">
					<input name="<?php echo $this->get_field_name( 'show_content' ); ?>" id="<?php echo $this->get_field_id( 'show_content' ); ?>" type="checkbox"<?php if ($show_content) echo ' checked="true"'; ?>>
					<label for="<?php echo $this->get_field_id( 'show_content' ); ?>">Content</label><br>
					<input name="<?php echo $this->get_field_name( 'show_time' ); ?>" id="<?php echo $this->get_field_id( 'show_time' ); ?>" type="checkbox"<?php if ($show_time) echo ' checked="true"'; ?>>
					<label for="<?php echo $this->get_field_id( 'show_time' ); ?>">Time</label>
				</div>
				<div class="alignright">
					<input name="<?php echo $this->get_field_name( 'show_author' ); ?>" id="<?php echo $this->get_field_id( 'show_author' ); ?>" type="checkbox"<?php if ($show_author) echo ' checked="true"'; ?>>
					<label for="<?php echo $this->get_field_id( 'show_author' ); ?>">Author</label><br>
					<input name="<?php echo $this->get_field_name( 'show_source' ); ?>" id="<?php echo $this->get_field_id( 'show_source' ); ?>" type="checkbox"<?php if ($show_source) echo ' checked="true"'; ?>>
					<label for="<?php echo $this->get_field_id( 'show_source' ); ?>">Source</label>
				</div>
			</p>
			<br class="clear">
			<?php
		}

		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['title'] = isset($new_instance['title']) ? $new_instance['title'] : $old_instance['title'];
			$instance['num'] = isset($new_instance['num']) ? strip_tags( $new_instance['num'] ) : $old_instance['title'];
			$instance['show_content'] = isset($new_instance['show_content']);
			$instance['show_time'] = isset($new_instance['show_time']);
			$instance['show_author'] = isset($new_instance['show_author']);
			$instance['show_source'] = isset($new_instance['show_source']);
			return $instance;
		}
	}
}

if ( !function_exists('register_stf_widget') )
{
	add_action( 'widgets_init', 'register_stf_widget' );
	function register_stf_widget() {
		register_widget( 'STF_Widget' );
	}
}