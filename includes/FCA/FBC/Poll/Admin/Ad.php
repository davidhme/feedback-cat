<?php

class FCA_FBC_Poll_Admin_Ad {
	public function display() {
		?>
		<style>
			.fca_fbc_ad_bar a {
				font-size: 14px;
				font-weight: bold;
			}

			.fca_fbc_ad_sidebar {
				position: absolute;
				top: 82px;
				right: 22px;
				width: 270px;
			}

			.fca_fbc_ad_sidebar .fca_fbc_centered {
				text-align: center;
			}

			.fca_fbc_ad_sidebar .button-large {
				font-size: 17px;
				line-height: 30px;
				height: 32px;
			}

			.fca_fbc_ad_input {
				width: 100%;
			}

			.fca_fbc_ad_form {
				border-top: 1px solid #fcfcfc;
				margin: 0 -11px;
				padding: 0 11px;
			}

			#wpbody-content {
				width: calc(100% - 296px);
			}

			#poststuff {
				min-width: 0;
			}
		</style>
		<div class="sidebar-container metabox-holder fca_fbc_ad_sidebar" id="fca_fbc_ad_sidebar">
			<div class="postbox">
				<h3 class="wp-ui-primary"><span>Get Feedback Like A Pro</span></h3>

				<div class="inside">
					<div class="main">
						<p class="fca_fbc_centered">
							The key to getting great feedback is asking great questions.
							Get tips, tricks and ready to use template questions in our free guide.
						</p>

						<form class="fca_fbc_ad_form" action="https://www.getdrip.com/forms/2885889/submissions" method="post" target="_blank">
							<p>
								<label for="fca_fbc_ad_input_email">Email</label>
								<input type="email" name="fields[email]" id="fca_fbc_ad_input_email" class="fca_fbc_ad_input" value="<?php

								echo htmlspecialchars( wp_get_current_user()->user_email )

								?>">
							</p>

							<div class="fca_fbc_centered">
								<input type="submit" name="submit" class="button-primary button-large" value="Free Download">
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<?php
	}
}
