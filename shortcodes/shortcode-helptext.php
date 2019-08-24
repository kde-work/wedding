<?php
function wb_helptext_shortcode( $atts, $content = null ) {
	if ( !is_null( $content ) && !is_feed() ) {
		extract( shortcode_atts( array(
			), $atts )
		);

		wb_scripts__helptext();

		ob_start();
		?>
		<div class="wb-helptext">
			<div class="wb-helptext__cont">
				<div class="wb-helptext__header"><div class="wb-helptext__close-icon wb-helptext__control" data-control="close">✕</div></div>
				<div class="wb-helptext__body">
					<?php
					echo do_shortcode($content);
					?>
				</div>
			</div>
			<div class="wb-helptext__icon wb-helptext__control" data-control="open">
				<img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iaXNvLTg4NTktMSI/Pgo8IS0tIEdlbmVyYXRvcjogQWRvYmUgSWxsdXN0cmF0b3IgMTkuMC4wLCBTVkcgRXhwb3J0IFBsdWctSW4gLiBTVkcgVmVyc2lvbjogNi4wMCBCdWlsZCAwKSAgLS0+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiBpZD0iQ2FwYV8xIiB4PSIwcHgiIHk9IjBweCIgdmlld0JveD0iMCAwIDUxMS45OTcgNTExLjk5NyIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNTExLjk5NyA1MTEuOTk3OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSIgd2lkdGg9IjUxMnB4IiBoZWlnaHQ9IjUxMnB4Ij4KPGNpcmNsZSBzdHlsZT0iZmlsbDojNUM1RTcwOyIgY3g9IjI1NS45OTgiIGN5PSIyNTUuOTk4IiByPSIyNTUuOTk4Ii8+CjxwYXRoIHN0eWxlPSJmaWxsOiNGRjUyNUQ7IiBkPSJNMTM2LjY5OSwwLjEzM0M2Mi44NSwxLjcxNCwyLjQwNiw2MS41MDUsMC4wNzIsMTM1LjMzM2MtMS4xNiwzNi42NzgsMTEuODIsNzAuMzA5LDMzLjg5Nyw5NS44NTkgIGM5Ljk0MywxMS41MDYsMTIuNzgxLDI3LjUzOSw3LjY2Niw0MS44NTlsLTkuMTk0LDI1Ljc0M2MtMi45OSw4LjM3Myw2LjcwNywxNS41MywxMy44MjcsMTAuMjA1bDMyLjYwOS0yNC4zODggIGM5LjcwMy03LjI1NywyMi4wMjQtOS45NTYsMzMuOTEyLTcuNjE0YzEyLjM3OSwyLjQzOSwyNS4zNDEsMy4yNDUsMzguNjcyLDIuMTQzYzcwLjEzNy01LjgwNCwxMjUuNDUtNjMuODA4LDEyNy45Ny0xMzQuMTQxICBDMjgyLjMyLDY0LjQ0NCwyMTcuMDAyLTEuNTg2LDEzNi42OTksMC4xMzN6Ii8+CjxwYXRoIHN0eWxlPSJmaWxsOiNGRkQxNUM7IiBkPSJNNDU0LjgxMywzNTkuNzc5Yy0xMS44MjgsMTQuMjczLTE1LjU3OSwzMy41NjItOS4zNTIsNTEuMDEybDQuNDIsMTIuMzgyICBjLTUuNTgsNi40NjgtMTEuNDgzLDEyLjY1NC0xNy42OCwxOC41MzZsLTI3LjQ2LTIwLjUxMWMtMTEuNTg4LTguNjQxLTI2LjIzNy0xMi4wNjgtNDAuNDQ4LTkuNDI1ICBjLTkuMTY0LDEuNzE0LTE5LjQyNSwzLjIyOS0yNi44NzUsMy4yMjljLTg0LjE2NiwwLTE1Mi4zODctNjguMjIxLTE1Mi4zODctMTUyLjM4N2MtMC4wMS04NC4wODIsNjguMjk0LTE1Mi4zODcsMTUyLjM4Ny0xNTIuMzg3ICBjODQuMTU2LDAsMTUyLjM4Nyw2OC4yMjEsMTUyLjM4NywxNTIuMzg3QzQ4OS44MDcsMjk5LjU0MSw0NzYuNjYyLDMzMy40MDYsNDU0LjgxMywzNTkuNzc5eiIvPgo8Zz4KCTxwYXRoIHN0eWxlPSJmaWxsOiNGOUVFRDc7IiBkPSJNMzU4LjkzMSwzNDYuOTExYzAsMTEuNTMtOS44NDMsMjEuNjU0LTIxLjM3MywyMS42NTRjLTEyLjM3NSwwLTIxLjY1NS0xMC4xMjQtMjEuNjU1LTIxLjY1NCAgIGMwLTExLjgxMyw5LjI4MS0yMS42NTUsMjEuNjU1LTIxLjY1NUMzNDkuMDg5LDMyNS4yNTYsMzU4LjkzMSwzMzUuMDk5LDM1OC45MzEsMzQ2LjkxMXogTTM1My41ODgsMjkzLjQ3NyAgIGMwLDguNzE5LTkuNTYzLDEyLjM3NS0xNi4zMTIsMTIuMzc1Yy05LDAtMTYuNTkyLTMuNjU2LTE2LjU5Mi0xMi4zNzVjMC0zMy40NjYtMy45MzYtODEuNTU2LTMuOTM2LTExNS4wMjQgICBjMC0xMC45NjgsOS0xNy4xNTUsMjAuNTI5LTE3LjE1NWMxMC45NjksMCwyMC4yNDksNi4xODYsMjAuMjQ5LDE3LjE1NUMzNTcuNTI0LDIxMS45MiwzNTMuNTg4LDI2MC4wMSwzNTMuNTg4LDI5My40Nzd6Ii8+Cgk8cGF0aCBzdHlsZT0iZmlsbDojRjlFRUQ3OyIgZD0iTTE2My43NSwxMzQuNDE2Yy0xNC4xODUsMTEuMDktMTQuNDQ1LDE4LjgyOS0xNC40NDUsMzIuMjQxYzAsNC45MDItMi41NzksMTAuNTc0LTE1LjIxOCwxMC41NzQgICBjLTEwLjU3NCwwLTE0LjE4NS0zLjg2OC0xNC4xODUtMTcuMjgxYzAtMjIuMTgyLDkuODAyLTMyLjc1NywxNy4yODEtMzkuMjA1YzguNTEyLTcuMjIyLDIyLjk1Ni0xNS4yMTgsMjIuOTU2LTI5LjE0NiAgIGMwLTExLjg2NC0xMC4zMTctMTcuNTQtMjMuMjE0LTE3LjU0Yy0yNi4zMDgsMC0yMC42MzUsMTkuODYxLTM0LjU2MiwxOS44NjFjLTYuOTY0LDAtMTUuNDc2LTQuNjQyLTE1LjQ3Ni0xNC43MDMgICBjMC0xMy45MjcsMTUuOTkyLTM0LjU2Miw1MC44MTMtMzQuNTYyYzMzLjAxNSwwLDU0LjkzOCwxOC4zMTMsNTQuOTM4LDQyLjU1OFMxNzAuNzE1LDEyOC45OTksMTYzLjc1LDEzNC40MTZ6IE0xNTQuMjA3LDIxNC44OSAgIGMwLDEwLjU3NC04Ljc3MSwxOS44Ni0xOS44NjEsMTkuODZjLTExLjA5LDAtMTkuNjAzLTkuMjg2LTE5LjYwMy0xOS44NmMwLTEwLjgzMyw4Ljc3MS0xOS44NjEsMTkuNjAzLTE5Ljg2MSAgIFMxNTQuMjA3LDIwNC4wNTcsMTU0LjIwNywyMTQuODl6Ii8+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPGc+CjwvZz4KPC9zdmc+Cg==" />
			</div>
		</div>
		<?php
		return ob_get_clean();
	}
	return '';
}
add_shortcode( 'wb_helptext', 'wb_helptext_shortcode' );