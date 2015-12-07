	<?php
	/**
	 * The template for displaying comments
	 *
	 * The area of the page that contains both current comments
	 * and the comment form.
	 *
	 * @package WordPress
	 * @subpackage Twenty_Fifteen
	 * @since Twenty Fifteen 1.0
	 */

	/*
	 * If the current post is protected by a password and
	 * the visitor has not yet entered the password we will
	 * return early without loading the comments.
	 */
	if ( post_password_required() ) {
		return;
	}
	?>

	<div id="comments" class="comments-area">

		<?php // if ( have_comments() ) : ?>
			
			<h2 class="comments-title">
				<?php
					$postID = get_the_ID();
					$now = current_time( 'mysql' );
					$args = array(
		                'date_query' => array(
		                    'after' => '5 minute ago',
		                    'before' => $now,
		                    'inclusive' => true,
		                	),
						'post_id' => $postID,
		        		'count' => true
					);
					$commentcount = get_comments($args);
					printf( _nx( 'Bids on &ldquo;%2$s&rdquo;', 'Bids on &ldquo;%2$s&rdquo;', $commentcount, 'comments title', 'twentyfifteen' ),
					number_format_i18n( $commentcount ), get_the_title() );
				?>
			</h2>

			<?php twentyfifteen_comment_nav(); ?>
			<!--<p>Auction Start Time: <?php // echo get_the_time(); ?><br>Current Time: <?php // echo $now; ?></p>-->

			<ol class="comment-list">
				<?php
				$args = array(
	                'date_query' => array(
	                    'after' => '5 minute ago',
	                    'before' => $now,
	                    'inclusive' => true,
	                	),
					'post_id' => $postID,
	        		'count' => true
				);
				$commentcount = get_comments($args);

				if ($commentcount == 0){

					// comment count in last 5 minutes is 0. 
					$args2 = array(
		                'date_query' => array(
		                    'after' => '16 week ago',
		                    'before' => $now,
		                    'inclusive' => true,
		                	),
						'post_id' => $postID,
						'status' => 'approve',
		        		'count' => true
					);

					$commentcount2 = get_comments($args2);

					if($commentcount2 != 0){
						$args3 = array(
			                'date_query' => array(
			                    'after' => '16 week ago',
			                    'before' => $now,
			                    'inclusive' => true,
			                	),
			                'status' => 'approve',
							'post_id' => $postID,
						);
						$comments3 = get_comments($args3);
						$historyStack = array();
						$historyBidID = array();
						foreach ( $comments3 as $comment ){
							array_push($historyStack, $comment->comment_content);
							array_push($historyBidID, $comment->comment_ID);
						}

						// Highest bid in history
						$highestBidIndex = array_search(max($historyStack), $historyStack);
						$highestBidID = $historyBidID[$highestBidIndex];

						echo "This item is sold.";
						echo "</br>Winning Bid is CAD "; print_r(max($historyStack));
						echo "</br>Winning Bidder is "; comment_author($highestBidID);
						echo "</br>Current Owner's Email Address: "; comment_author_email($highestBidID);

						echo "</br></br>Previous owner of the item: "; the_author_meta('user_email');

						//$to2 = "'".the_author_meta(user_email)."'";
						wp_mail( 'sa.priom@gmail.com', 'Montreal Auction', 'You have won an auction.' );
						//wp_mail( $to, 'Montreal Auction', 'You have a winner for your auction' );*/
					
					}


					elseif($commentcount2 == 0){
						echo "<p>This auction is currently running</p>";
						$comments_args = array( 
        					'label_submit'=>'Post Your Bid',
				        	'title_reply'=>'Write a Reply or Comment',
				        	'comment_notes_after' => '',
						);
						comment_form($comments_args);
					}

				}

				else{

					// Display comments from last 5 minutes
					$comments = get_comments( 
	    				[
	        				'date_query' => [
	            				'after'     => '5 minutes ago',
	            				'inclusive' => true,
	        				],
	        				'post_id' => $postID,
	        				'status'  => 'approve',
	    				]
					);
					printf( wp_list_comments( $args = [ 'echo' => 0 ], $comments ));

					// Retrieve bids and push them in an array
					$args4 = array(
		                'date_query' => array(
		                    'after' => '5 minute ago',
		                    'before' => $now,
		                    'inclusive' => true,
		                	),
						'post_id' => $postID,
					);
					$comments4 = get_comments($args4);
					$stackBidValue = array();
					$stackBidID = array();
					foreach ( $comments4 as $comment ){
						array_push($stackBidValue, $comment->comment_content);
						array_push($stackBidID, $comment->comment_ID);					
					}

					// Highest bid in last minutes
					$highestBidIndex = array_search(max($stackBidValue), $stackBidValue);
					$highestBidID = $stackBidID[$highestBidIndex];

					echo "<p>Current highest bid: "; print_r(max($stackBidValue));
					echo "<br>User: "; comment_author($highestBidID);
					echo "</p>";

					$comments_args = array( 
        				'label_submit'=>'Post Your Bid',
				        'title_reply'=>'Write a Reply or Comment',
				        'comment_notes_after' => '',
					);
					comment_form($comments_args);
				}
			?>

			</ol>

			<?php twentyfifteen_comment_nav(); ?>

		<?php //endif; // have_comments() ?>

		<?php
			// If comments are closed and there are comments, let's leave a little note, shall we?		
			if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
		?>
			<p class="no-comments"><?php _e( 'The auction is closed.', 'twentyfifteen' ); ?></p>
		<?php endif; ?>

		<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
		<script>
			$("#comment").on("keypress keyup blur",function (event) {
	          $(this).val($(this).val().replace(/[^\d].+/, ""));
	           if ((event.which < 48 || event.which > 57)) {
	               event.preventDefault();
	           }
	       	});
		</script>

	</div>