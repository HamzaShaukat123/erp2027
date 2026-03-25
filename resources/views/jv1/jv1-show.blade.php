@include('../layouts.header')
	<body>
		<section class="body">
			<div class="inner-wrapper">
				<section role="main" class="content-body" style="margin:0px;padding:75px 10px !important">
					@include('../layouts.pageheader')
                    <section class="card">
						<div class="card-body">
							<div class="invoice">
								<header class="clearfix">
									<div class="row">
										<div class="col-8 mt-3 mb-3">
											<h2 class="h2 mt-0 mb-1" style="color:#17365D">Voucher NO:</h2>
											<h4 class="h4 m-0 text-dark font-weight-bold">JV1-{{$jv1->auto_lager}}</h4>
										</div>
										<div class="col-4 text-end mt-3 mb-3">
											<div class="ib">
												<img width="100px" src="/assets/img/logo.png" alt="MFI Logo" />
											</div>
										</div>
									</div>
								</header>

								<div class="bill-info">
									<div class="row">
										<div class="col-md-7">
											<div class="bill-to">
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Date: &nbsp </span>
													<span style="font-weight:400;color:black" class="value"> {{\Carbon\Carbon::parse($jv1->date)->format('d-m-y')}}</span>
												</h4>
											</div>
										</div>
										<div class="col-md-5">
											<div class="bill-data">
												<h4 class="mb-0 h6 mb-1 text-dark font-weight-semibold">
													<span style="color:#17365D">Remarks: &nbsp</span>
													<span style="font-weight:400;color:black" class="value"> {{$jv1->remarks}}</span>
												</h4>
											</div>
										</div>
									</div>
								</div>

								<table class="table table-responsive-md table-striped invoice-items" style="font-size: 18px;">
									<thead>
										<tr class="text-dark">
											<th width="40%" class="text-center font-weight-semibold" style="color:#17365D;">Account Debit</th>
											<th width="40%" class="text-center font-weight-semibold" style="color:#17365D;">Account Credit</th>
											<th width="20%" class="text-center font-weight-semibold" style="color:#17365D;">Amount</th>
										</tr>
									</thead>
									<tbody>
										<tr>
											<td class="font-weight-semibold text-dark text-center">{{$jv1->debit_account}}</td>
											<td class="font-weight-semibold text-dark text-center">{{$jv1->credit_account}}</td>
											<td class="font-weight-semibold text-dark text-center">{{number_format($jv1->amount)}}</td>
										</tr>
									</tbody>
								</table>
								
								<div class="row mt-3">
									<div class="col-12 col-md-4 ms-auto text-end">
										<a onclick="window.location='{{ route('all-jv1') }}'" class="btn btn-primary">
											<i class="fas fa-arrow-left"></i> Back
										</a>

										<button type="button"
											class="btn btn-warning modal-with-zoom-anim ws-normal modal-with-form"
											onclick="getJVSDetails({{ $jv1->auto_lager }})"
											href="#updateModal"
											title="Edit JV1">
											<i class="fas fa-edit"></i> Edit
										</button>

										<a href="{{ route('print-jv1', $jv1->auto_lager) }}" class="btn btn-danger" target="_blank">
											<i class="fas fa-print"></i> Print
										</a>
									</div>
								</div>

							</div>
						</div>

					</section>
				</section>
			</div>
			</div>
		</section>

		 <div id="updateModal" class="modal-block modal-block-primary mfp-hide" style="z-index: 1050">
            <section class="card">
                <form method="post" action="{{ route('update-jv1') }}" enctype="multipart/form-data" onkeydown="return event.key != 'Enter';">
                    @csrf
                    <header class="card-header">
                        <h2 class="card-title">Update Journal Voucher</h2>
                    </header>
                    <div class="card-body">
                        <div class="row form-group">
                            <div class="col-lg-6">
                                <label>JV1 Code</label>
                                <input type="number" class="form-control" placeholder="JV1 Code" id="update_id" required disabled>
                                <input type="hidden" class="form-control" placeholder="JV1 Code" name="update_auto_lager" id="update_id_view" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Date</label>
                                <input type="date" class="form-control" placeholder="Date" id="update_date" name="update_date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Account Debit<span style="color: red;"><strong>*</strong></span></label>
                                <select data-plugin-selecttwo class="form-control select2-js"  name="update_ac_dr_sid" id="update_ac_dr_sid" required >
                                    <option value="" disabled selected>Select Account</option>
                                    @foreach($acc as $key => $row)	
                                        <option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Account Credit<span style="color: red;"><strong>*</strong></span></label>
                                <select data-plugin-selecttwo class="form-control select2-js"  name ="update_ac_cr_sid" required id="update_ac_cr_sid">
                                    <option disabled selected>Select Account</option>
                                    @foreach($acc as $key => $row)	
                                        <option value="{{$row->ac_code}}">{{$row->ac_name}}</option>
                                    @endforeach
                                </select>                            
                            </div>
                            <div class="col-lg-6 mb-2">
                                <label>Amount<span style="color: red;"><strong>*</strong></span></label>
                                <input type="number" class="form-control" placeholder="Amount" id="update_amount" value="0" step="any" name="update_amount" required>
                            </div>

                            <div class="col-lg-6 mb-2">
                                <label>Attachments</label>
                                <input type="file" class="form-control" name="update_att[]" multiple accept=".zip, appliation/zip, application/pdf, image/png, image/jpeg">
                            </div>  
                            <div class="col-lg-12 mb-2">
                                <label>Remarks</label>
                                <textarea rows="4" cols="50" class="form-control cust-textarea" placeholder="Remarks" id="update_remarks" name="update_remarks"> </textarea>
                            </div>

                            
                            <div class="col-lg-6 mb-2">
                                <label>Unadjusted Sales Ageing Voucher</label>
                                <input type="checkbox" id="ignore_sale_jv2">
                                <select data-plugin-selecttwo class="form-control select2-js" name ="update_jv2_id_sale" id="update_jv2_id_sale">
                                    <option value="" disabled selected>Select JV2-ID</option>
                                    @foreach($jv2_id_sale as $key => $row1)	
                                        <option value="{{ $row1->jv2_id }}"> {{ $row1->prefix }}{{ $row1->jv2_id }}  ({{ $row1->ac_name }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-6 mb-2">
                                <label>Unadjusted Purchase Ageing Voucher</label>
                                <input type="checkbox" id="ignore_pur_jv2">
                                <select data-plugin-selecttwo class="form-control select2-js" name ="update_jv2_id_pur" id="update_jv2_id_pur">
                                    <option value="" disabled selected>Select JV2-ID</option>
                                    @foreach($jv2_id_pur as $key => $row2)	
                                        <option value="{{ $row2->jv2_id }}"> {{ $row2->prefix }}{{ $row2->jv2_id }}  ({{ $row2->ac_name }})</option>
                                    @endforeach
                                </select>
                            </div>
                           
                        </div>
                    
                    <footer class="card-footer">
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">Update Journal Voucher</button>
                                <button class="btn btn-default modal-dismiss">Cancel</button>
                            </div>
                        </div>
                    </footer>
                    </div>
                </form>
            </section>
        </div>


        @include('../layouts.footerlinks')
	</body>


	<script>
		
		var netAmount = @json($jv1->amount);
		var words = convertCurrencyToWords(netAmount);
		document.getElementById('numberInWords').innerHTML = words;

		function getJVSDetails(id){
			$.ajax({
				type: "GET",
				url: "/vouchers/detail",
				data: {id:id},
				success: function(result){
					$('#update_id').val(result['auto_lager']);
					$('#update_id_view').val(result['auto_lager']);
					$('#update_ac_cr_sid').val(result['ac_cr_sid']).trigger('change');
					$('#update_ac_dr_sid').val(result['ac_dr_sid']).trigger('change');
					$('#update_amount').val(result['amount']);
					$('#update_date').val(result['date']);
					$('#update_remarks').val(result['remarks']);
					$('#update_jv2_id_sale').val(result['jv2_id_sale']).trigger('change');
					$('#update_jv2_id_pur').val(result['jv2_id_pur']).trigger('change');
				},
				error: function(){
					alert("error");
				}
			});
		}

	</script>
	
	
</html>