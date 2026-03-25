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
										<h4 class="h4 m-0 text-dark font-weight-bold">JV2-{{$jv2->jv_no}}</h4>
									</div>
									<div class="col-4 text-end mt-3 mb-3">
										<img width="100px" src="/assets/img/logo.png" alt="MFI Logo" />
									</div>
								</div>
							</header>

							<div class="bill-info">
								<div class="row align-items-center">
									<div class="col-md-7">
										<div class="bill-to">
											<h4 class="h6 mb-0 text-dark font-weight-semibold d-flex align-items-center">
												<span style="color:#17365D;">Date:&nbsp;</span>
												<span style="font-weight:400; color:black;">
													{{ \Carbon\Carbon::parse($jv2->jv_date)->format('d-m-y') }}
												</span>
											</h4>
										</div>
									</div>

									<div class="col-md-5">
										<div class="bill-data d-flex justify-content-between align-items-center">
											<h4 class="h6 mb-0 text-dark font-weight-semibold mb-1">
												<span style="color:#17365D;">Narration:&nbsp;</span>
												<span style="font-weight:400; color:black;">
													{{ $jv2->narration }}
												</span>
											</h4>

											<a href="#attModal"
												onclick="getAttachements({{ $jv2->jv_no }})"
												class="modal-with-zoom-anim text-primary"
												style="font-size:13px; text-decoration:underline;">
												Show Attachment
											</a>
										</div>
									</div>
								</div>
							</div>

							<table class="table table-responsive-md table-striped invoice-items" style="font-size: 18px;">
								<thead>
									<tr class="text-dark">
										<th width="20%" class="text-center font-weight-semibold" style="color:#17365D;">Account Name</th>
										<th width="20%" class="text-center font-weight-semibold" style="color:#17365D;">Remarks</th>
										<th width="15%" class="text-center font-weight-semibold" style="color:#17365D;">Bank Name</th>
										<th width="15%" class="text-center font-weight-semibold" style="color:#17365D;">Inst/Slip #</th>
										<th width="15%" class="text-center font-weight-semibold" style="color:#17365D;">Debit</th>
										<th width="15%" class="text-center font-weight-semibold" style="color:#17365D;">Credit</th>
									</tr>
								</thead>
								<tbody>
									@foreach($entries as $entry)
										<tr>
											<td class="font-weight-semibold text-dark text-center">{{ $entry->account_name }}</td>
											<td class="font-weight-semibold text-dark text-center">{{ $entry->remarks }}</td>
											<td class="font-weight-semibold text-dark text-center">{{ $entry->bankname }}</td>
											<td class="font-weight-semibold text-dark text-center">
												{{ $entry->instrumentnumber && $entry->slip 
													? $entry->instrumentnumber . '-' . $entry->slip 
													: ($entry->instrumentnumber ?? $entry->slip) }}
											</td>
											<td class="font-weight-semibold text-dark text-center">{{ number_format($entry->debit) }}</td>
											<td class="font-weight-semibold text-dark text-center">{{ number_format($entry->credit) }}</td>
										</tr>
									@endforeach
								</tbody>
							</table>
							{{-- ✅ Sales & Purchase Ageing Sections (Side by Side) --}}
							<div class="row mt-4">

								{{-- ✅ Sales Ageing Section --}}
								<div class="col-sm-12 col-md-6 col-lg-6 mb-3">								
									<section class="card h-100">
										<header class="card-header d-flex justify-content-between align-items-center">
											<h2 class="card-title">
												Sales Ageing 
												<span id="sale_span" style="color:red;font-size:16px;display:none">More than 1 credit not allowed</span>
												<span id="sales_warning" style="color:red;font-size:16px;display:none">All Previous Sales Ageing Record Against this JV2 will be replaced by latest</span>
											</h2>
										</header>

										@if(!empty($sales_ageing))
											<div class="card-body">
												<div class="row form-group mb-2">
													<div class="col-3 mb-2">
														<label class="col-form-label">
															Account Name 
															<a onclick="refreshSalesAgeing()" id="refreshBtn" style="display:none">
																<i class="bx bx-refresh" style="font-size:20px;color:red;"></i>
															</a>
														</label>
														<select data-plugin-selecttwo class="form-control select2-js" id="customer_name" name="customer_name" onchange="getPendingInvoices()" required disabled>
															<option value="0" selected>Select Account</option>
															@foreach($acc as $key1 => $row1)	
																<option value="{{$row1->ac_code}}" {{ $sales_ageing[0]->account_name == $row1->ac_code ? 'selected' : '' }}>{{$row1->ac_name}}</option>
															@endforeach
														</select>
													</div>

													<div class="col-3 mb-2">
														<label class="col-form-label">Unadjusted Amount</label>
														<input type="number" id="sales_unadjusted_amount" name="sales_unadjusted_amount" value="0" class="form-control" disabled step="any">
													</div>

													<div class="col-3 mb-2">
														<label class="col-form-label">Total Amount</label>
														<input type="number" id="total_reci_amount" class="form-control" value="0" disabled step="any">
													</div>

													<div class="col-3 mb-2">
														<label class="col-form-label">Remaining Amount</label>
														<input type="number" id="sales_ageing_remaing_amt" class="form-control" value="0" disabled step="any">
													</div>

													<div class="col-12 mb-2">
														<table id="sales_ageing" class="table table-bordered table-striped mb-0 mt-2">
															<thead>
																<tr>
																	<th width="13%">Inv #</th>
																	<th width="12%">Date</th>
																	<th width="17%">Bill Amount</th>
																	<th width="17%">Remaining</th>
																	<th width="17%">Amount</th>
																	<th width="14%">Name</th>
																</tr>
															</thead>
															<tbody id="pendingInvoices">
																@foreach ($sales_ageing as $key => $row)
																	<tr>
																		<td>
																			<input type='text' class='form-control' value="{{$row->prefix}}{{$row->Sal_inv_no}}" disabled>
																			<input type='hidden' name='invoice_nos[]' value="{{$row->Sal_inv_no}}">
																			<input type='hidden' name='prefix[]' value="{{$row->prefix}}">
																		</td>
																		<td><input type='date' class='form-control' value="{{$row->sa_date}}" disabled></td>
																		<td><input type='number' class='form-control' value="{{$row->b_amt}}" disabled></td>
																		<td><input type='number' class='form-control text-danger' value="{{$row->balance}}" disabled></td>
																		<td><input type='number' class='form-control' value="{{$row->amount}}" step='any' disabled></td>
																		<td><input type='text' class='form-control' value="{{$row->nop}}" disabled></td>
																	</tr>
																@endforeach
															</tbody>
														</table>										
													</div>
												</div>
											</div>
										@else
											<div class="card-body">
												<div class="row form-group mb-2">
													<div class="col-3 mb-2">
														<label class="col-form-label">Account Name</label>
														<select data-plugin-selecttwo class="form-control select2-js" id="customer_name" name="customer_name" onchange="getPendingInvoices()" disabled required>
															<option value="0" selected>Select Account</option>
															@foreach($acc as $key1 => $row1)	
																<option value="{{$row1->ac_code}}">{{$row1->ac_name}}</option>
															@endforeach
														</select>
													</div>

													<div class="col-3 mb-2">
														<label class="col-form-label">Unadjusted Amount</label>
														<input type="number" id="sales_unadjusted_amount" name="sales_unadjusted_amount" value="0" class="form-control" disabled step="any">
													</div>

													<div class="col-3 mb-2">
														<label class="col-form-label">Total Amount</label>
														<input type="number" id="total_reci_amount" class="form-control" value="0" disabled step="any">
													</div>

													<div class="col-3 mb-2">
														<label class="col-form-label">Remaining Amount</label>
														<input type="number" id="sales_ageing_remaing_amt" class="form-control" value="0" disabled step="any">
													</div>

													<div class="col-12 mb-2">
														<table id="sales_ageing" class="table table-bordered table-striped mb-0 mt-2">
															<thead>
																<tr>
																	<th width="13%">Inv #</th>
																	<th width="12%">Date</th>
																	<th width="17%">Bill Amount</th>
																	<th width="17%">Remaining</th>
																	<th width="17%">Amount</th>
																	<th width="14%">Name</th>
																</tr>
															</thead>
															<tbody id="pendingInvoices">
																<tr></tr>
															</tbody>
														</table>										
													</div>
												</div>
											</div>
										@endif
									</section>
								</div>

								{{-- ✅ Purchase Ageing Section --}}
								<div class="col-sm-12 col-md-6 col-lg-6 mb-3">								
									<section class="card h-100">
										<header class="card-header d-flex justify-content-between align-items-center">
											<h2 class="card-title">
												Purchase Ageing 
												<span id="pur_span" style="color:red;font-size:16px;display:none">More than 1 Debit not allowed</span>
												<span id="pur_warning" style="color:red;font-size:16px;display:none">All Previous Purchase Ageing Record Against this JV2 will be replaced by latest</span>
											</h2>
										</header>

										@if(!empty($purchase_ageing))
											<div class="card-body">
												<div class="row form-group mb-2">
													<div class="col-3 mb-2">
														<label class="col-form-label">
															Account Name 
															<a onclick="refreshPurAgeing()" id="PurrefreshBtn" style="display:none">
																<i class="bx bx-refresh" style="font-size:20px;color:red;"></i>
															</a>
														</label>
														<select data-plugin-selecttwo class="form-control select2-js" id="pur_customer_name" name="pur_customer_name" onchange="getPurPendingInvoices()" required disabled>
															<option value="0" disabled selected>Select Account</option>
															@foreach($acc as $key1 => $row1)
																<option value="{{ $row1->ac_code }}" {{ $purchase_ageing[0]->acc_name == $row1->ac_code ? 'selected' : '' }}>
																	{{ $row1->ac_name }}
																</option>
															@endforeach
														</select>
													</div>

													<div class="col-3 mb-2">
														<label class="col-form-label">Unadjusted Amount</label>
														<input type="number" id="pur_unadjusted_amount" name="pur_unadjusted_amount" value="0" class="form-control" disabled step="any">
													</div>

													<div class="col-3 mb-2">
														<label class="col-form-label">Total Amount</label>
														<input type="number" id="total_pay_amount" value="0" class="form-control" disabled step="any">
													</div>

													<div class="col-3 mb-2">
														<label class="col-form-label">Remaining Amount</label>
														<input type="number" id="pur_ageing_remaing_amt" class="form-control" value="0" disabled step="any">
													</div>

													<div class="col-12 mb-2">
														<table class="table table-bordered table-striped mb-0 mt-2">
															<thead>
																<tr>
																	<th>Inv #</th>
																	<th>Date</th>
																	<th>Bill Amount</th>
																	<th>Remaining Amount</th>
																	<th>Amount</th>
																</tr>
															</thead>
															<tbody id="purpendingInvoices">
																@foreach ($purchase_ageing as $key => $row)
																	<tr>
																		<td>
																			<input type="text" class="form-control" value="{{ $row->sales_prefix }}{{ $row->sales_id }}" disabled>
																			<input type="hidden" name="invoice_nos[]" value="{{ $row->sales_id }}">
																			<input type="hidden" name="pur_totalInvoices" value="{{ $key }}">
																			<input type="hidden" name="sales_prefix[]" value="{{ $row->prefix }}">
																		</td>
																		<td><input type="date" class="form-control" value="{{ $row->sa_date }}" disabled></td>
																		<td><input type="number" class="form-control" value="{{ $row->b_amt }}" name="bill_amount[]" disabled></td>
																		<td><input type="number" class="form-control text-danger" value="{{ $row->balance }}" name="balance_amount[]" disabled></td>
																		<td><input type="number" class="form-control" value="{{ $row->amount }}" max="{{ $row->amount }}" step="any" name="rec_amount[]" onchange="totalPay()" required disabled></td>
																	</tr>
																@endforeach
															</tbody>
														</table>										
													</div>
												</div>
											</div>
										@else
											<div class="card-body">
												<div class="row form-group mb-2">
													<div class="col-3 mb-2">
														<label class="col-form-label">
															Account Name 
															<a onclick="refreshPurAgeing()" id="PurrefreshBtn" name="pur_customer_name" style="display:none">
																<i class="bx bx-refresh" style="font-size:20px;color:red;"></i>
															</a>
														</label>
														<select data-plugin-selecttwo class="form-control select2-js" name="pur_customer_name" id="pur_customer_name" onchange="getPurPendingInvoices()" required disabled>
															<option value="0" disabled selected>Select Account</option>
															@foreach($acc as $key1 => $row1)	
																<option value="{{ $row1->ac_code }}">{{ $row1->ac_name }}</option>
															@endforeach
														</select>
													</div>

													<div class="col-3 mb-2">
														<label class="col-form-label">Unadjusted Amount</label>
														<input type="number" id="pur_unadjusted_amount" name="pur_unadjusted_amount" value="0" class="form-control" disabled step="any">
													</div>

													<div class="col-3 mb-2">
														<label class="col-form-label">Total Amount</label>
														<input type="number" id="total_pay_amount" value="0" class="form-control" disabled step="any">
													</div>

													<div class="col-3 mb-2">
														<label class="col-form-label">Remaining Amount</label>
														<input type="number" id="pur_ageing_remaing_amt" class="form-control" value="0" disabled step="any">
													</div>

													<div class="col-12 mb-2">
														<table class="table table-bordered table-striped mb-0 mt-2">
															<thead>
																<tr>
																	<th>Inv #</th>
																	<th>Date</th>
																	<th>Bill Amount</th>
																	<th>Remaining Amount</th>
																	<th>Amount</th>
																</tr>
															</thead>
															<tbody id="purpendingInvoices">
																<tr></tr>
															</tbody>
														</table>										
													</div>
												</div>
											</div>
										@endif
									</section>
								</div>
							</div>
							{{-- ✅ End Combined Ageing Row --}}



							{{-- ✅ Buttons Aligned to Bottom-Right Corner --}}
							<div class="row mt-4">
								<div class="col-12">
									<div class="d-flex justify-content-end">
										<a onclick="window.location='{{ route('all-jv2') }}'" class="btn btn-primary me-2">
											<i class="fas fa-arrow-left"></i> Back
										</a>
										<a href="{{ route('edit-jv2', $jv2->jv_no) }}" class="btn btn-warning me-2">
											<i class="fas fa-edit"></i> Edit
										</a>
										<a href="{{ route('print-jv2', $jv2->jv_no) }}" class="btn btn-danger" target="_blank">
											<i class="fas fa-print"></i> Print
										</a>
									</div>
								</div>
							</div>

						</div>
					</div>
				</section>
			</section>
		</div>
	</section>

	{{-- Attachments Modal --}}
	<div id="attModal" class="zoom-anim-dialog modal-block modal-block-danger mfp-hide">
		<section class="card">
			<header class="card-header">
				<h2 class="card-title">All Attachments</h2>
			</header>
			<div class="card-body">
				<table class="table table-bordered table-striped mb-0" id="datatable-default">
					<thead>
						<tr>
							<th>Attachment Path</th>
							<th>Download</th>
							<th>View</th>
							<th>Delete</th>
						</tr>
					</thead>
					<tbody id="jv2_attachements"></tbody>
				</table>
			</div>
			<footer class="card-footer text-end">
				<button class="btn btn-default modal-dismiss">Cancel</button>
			</footer>
		</section>
	</div>

	@include('../layouts.footerlinks')
</body>

<script>
	var netAmount = <?php echo json_encode($jv2->amount); ?>;
	var words = convertCurrencyToWords(netAmount);
	document.getElementById('numberInWords').innerHTML = words;

	function getAttachements(id) {
		$('#jv2_attachements').empty();
		$.ajax({
			type: "GET",
			url: "/vouchers2/attachements",
			data: { id: id },
			success: function(result) {
				$.each(result, function(k, v) {
					var html = "<tr>";
					html += "<td>" + v['att_path'] + "</td>";
					html += "<td class='text-center'><a class='text-danger' href='/vouchers2/download/" + v['att_id'] + "'><i class='fas fa-download'></i></a></td>";
					html += "<td class='text-center'><a class='text-primary' href='/vouchers2/view/" + v['att_id'] + "' target='_blank'><i class='fas fa-eye'></i></a></td>";
					html += "<td class='text-center'><a class='text-primary' href='#' onclick='deleteFile(" + v['att_id'] + ")'><i class='fas fa-trash'></i></a></td>";
					html += "</tr>";
					$('#jv2_attachements').append(html);
				});
			},
			error: function() {
				alert("Error fetching attachments.");
			}
		});
	}
</script>
</html>
