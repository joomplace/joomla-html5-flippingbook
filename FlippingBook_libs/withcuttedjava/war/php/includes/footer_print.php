		
		<footer>
			<p class="curator">Published by Google 2010</p>
		</footer>
		

		<script type="text/javascript" src="/js/thirdparty/jquery.1.4.2.min.js"></script>		
		<script type="text/javascript"> 

			$( 'img' ).each( function(){
				$(this).attr( 'src', $(this).attr('data-src') );
			});
			window.onload = function() {
				setTimeout(function(){
					window.print();
				}, 500);
			}
		</script> 
		
	</body>
</html>