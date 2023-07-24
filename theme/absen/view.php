<?php

(!defined('THEMEPATH'))?exit:'';

require dirname(__FILE__).'/scripts.php';

class sobad_absen extends absen_control{
	protected static $data = '';

	public static function load_here($args=array()){
		self::$data = $args;
		self::_html();
		parent::_control();
		self::_script();
	}

	private static function _html(){
		$args = self::$data;

		?>
		<div class="layout-absen">
			<div class="content-absen">
				<div class="row">
					<div class="col-md-9 padding-box">
						<div class="row">
							<div class="layout-absen-work">
								<div class="col-md-10 padding-box">
									<div class="row">
										<div id="employee-work" class="box-work-flex">
											
										</div>
										<div id="employee-exclude" class="box-work-flex">
											
										</div>
										<div id="internship-work" class="box-work-flex">
											
										</div>
									</div>
								</div>
								<div class="col-md-2 padding-box">
									<?php self::_employee_excity() ;?>
								</div>
								<div id="employee-animation">
									
								</div>
							</div>
							<div id="absen-statistik">
								<?php //_employee_statistik() ;?>
							</div>
							<div id="absen-notwork">
								<?php self::_employee_notwork() ;?>
							</div>
						</div>
					</div>
					<div class="col-md-3 padding-box">
						<?php
							self::absen_box_right($args['status']);
						?>
					</div>
				</div>
			</div>

			<!-- Modal -->
			<div class="modal fade" id="myModal" role="dialog">
				<div class="modal-dialog">
			    
					<!-- Modal content-->
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal">&times;</button>
							<h4 class="modal-title">Solo Abadi</h4>
						</div>
						<div class="modal-body">
							
						</div>
						<div class="modal-footer">
						</div>
					</div>
				</div>
			</div>

		</div>
		<?php
	}

	private static function _employee_excity(){
		?>
			<div id="employee-excity" class="row absen-exwork">
        		
       		</div>
    		<div class="row absen-work">
        		<div class="col-md-6 padding-box">
        			<div class="display-table">
        				<div class="display-table-cell">
        					<div class="text-work-layout">
        						<span>Team</span>
        						<label>Masuk</label>
        					</div>
        				</div>
        			</div>
        		</div>
        		<div class="col-md-6 padding-box">
        			<div class="display-table">
        				<div class="display-table-cell">
        					<div id="total-work-absen">
        						0
        					</div>
        				</div>
        			</div>
        		</div>
			</div>
		<?php
	}

	private static function _employee_notwork(){
		?>
			<div class="layout-notwork">
				<div id="multiSlider" class="carousel slide" data-ride="carousel">
				<!-- Wrapper for slides -->
				    <div id="slider-notwork" class="MS-content" role="listbox">

				    </div>

			      <!-- Left and right controls -->
					<div class="MS-controls">
						<div class="layout-control">
	                        <button class="MS-left carousel-control">
	                        	<span class="glyphicon glyphicon-chevron-left"></span>
						   		<span class="sr-only">Previous</span>
	                        </button>
	                        <button class="MS-right carousel-control">
	                        	<span class="glyphicon glyphicon-chevron-right"></span>
							    <span class="sr-only">Next</span>
	                        </button>
	                   	</div>
                    </div>
				</div>
			</div>
		<?php
	}

/*
	public function _employee_box($data=array()){
		// Sample jadi HTML layout_user -----

		$class = isset($data['class'])?$data['class']:'';
		$image = isset($data['image'])?$data['image']:'no-profile.jpg';
		$name = isset($data['name'])?$data['name']:'no name';
		$time = isset($data['time'])?$data['time']:'-';

		?>
			<div class="absen-content <?php print($class) ;?>"> 
				<div class="image-content">
					<img src="asset/img/user/<?php print($image) ;?>">
				</div>
				<div class="employee name-content"> <?php print($name) ;?> </div>				
				<div class="employee time-content"> <?php print($time) ;?> </div>
			</div>
		<?php
	}
*/

	private static function absen_box_right($args=array()){
		$total = !isset($args['total'])?0:$args['total'];
		$intern = !isset($args['intern'])?0:$args['intern'];

		$masuk = !isset($args['masuk'])?0:$args['masuk'];
		$cuti = !isset($args['cuti'])?0:$args['cuti'];
		$izin = !isset($args['izin'])?0:$args['izin'];
		$sick = !isset($args['sakit'])?0:$args['sakit'];
		$luar_kota = !isset($args['luar kota'])?0:$args['luar kota'];
//		$tugas = !isset($args['tugas'])?0:$args['tugas'];
//		$libur = !isset($args['libur'])?0:$args['libur'];

		$video = !isset($args['video'])?array():$args['video'];

		$args = array(
		//	'Masuk' 	=> $masuk,
			'absen-dayoff'	=> array('Cuti', $cuti),
			'absen-sick'	=> array('Sakit', $sick),
			'absen-permit'	=> array('Izin', $izin),
			'absen-outcity'	=> array('Luar Kota', $luar_kota),
//			'absen-tugas'	=> array('Tugas Luar', $tugas),
//			'absen-holiday'	=> array('Libur', $libur),
		);

		?>
			<div class="absen_datetime">
				<div class="absen_date">
					<div class="absen_day">
						<?php print(conv_day_id(date('Y-m-d'))) ;?>
					</div>
                	<span>
                		<?php echo format_date_id(date('Y-m-d')) ;?>
                	</span>
			    </div>
			    <div id="absen_time">
			    	<?php echo date('H:i') ;?>
			    </div>
			</div>
			<div class="absen_info">
				<div class="info_video">
					<div class="layout_video">
	                    <div class="frame_video">
	                    	<video id="video-profile" autoplay>
	                    		<?php

	                    			foreach ($video as $key => $val) {
	                    				echo '<source class="active" src="'.$val.'" type="video/mp4">';
	                    			}
	                    		?>

								Your browser does not support the video tag.
							</video>
	                    </div>
	                    <div class="play_video default-sobad" style="opacity: 0;">
                        	<i class="fa fa-play"></i>
	                    </div>
	                    <div class="text_video title-content" style="opacity: 0;">
	                        <label> Staff <span class="default-sobad">Profile </span></label>
	                    </div>
	                </div>
                </div>
                <div class="info_note">
                	<div class="total_absen">
                		<div class="display-table">
                			<div class="display-table-cell">
		                    	<label>Keterangan</label>
		                    	<span id="total-employee">Karyawan Total : <?php print($total) ;?></span>
		                    	<span id="total-internship">Internship Total : <?php print($intern) ;?></span>
		                    </div>
		                </div>
		            </div>
		            <div class="note_detail">
		            	<div class="display-table">
                			<div class="display-table-cell">
				                <table>
				                   	<tbody>
		        		            	<?php
		                		    		foreach ($args as $key => $val) {
		                    					echo '
		                    						<tr>
		                    							<td style="width:75%;">'.$val[0].'</td>
		                    							<td style="width:10%;">:</td>
		                    							<td id="'.$key.'" style="width:15%;"> '.$val[1].'</td>
		                    						</tr>
		                    					';
		                    				}
		                   				?>
		                    		</tbody>
				                </table>
				            </div>
				        </div>
		            </div>
                </div>
			</div>
		<?php
	}

	private static function _script(){
		?>
			<script type="text/javascript">
				setInterval(time_absen,1000);

				function time_absen(){
					var j;var m; var d;
					var waktu = new Date();

					j = waktu.getHours().toString();
					if(j.length<2){
						j = '0'+j;
					}

					m = waktu.getMinutes().toString();
					if(m.length<2){
						m = '0'+m;
					}
/*
					d = waktu.getSeconds().toString();
					if(d.length<2){
						d = '0'+d;
					}
*/
					var wkt = j+':'+m;
 
					$('#absen_time').html(wkt);
				}
			</script>
		<?php
	}
}