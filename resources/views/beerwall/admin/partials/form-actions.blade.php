					<div class="pt-6 border-t border-gray-100 flex items-center justify-between gap-4">

						<a
								href="{{ route('beerwall.admin.index') }}"
								class="text-gray-500 hover:text-gray-700 hover:underline text-sm font-medium"
						>
							Anuluj
						</a>

						<button
								type="submit"
								class="inline-flex items-center gap-2
                                   px-6 py-3
                                   bg-blue-600
                                   border border-transparent
                                   rounded-md
                                   font-bold text-sm text-white
                                   uppercase tracking-widest
                                   hover:bg-blue-700
                                   focus:outline-none
                                   focus:ring
                                   ring-blue-300
                                   transition duration-150
                                   shadow-md"
						>

							@if(isset($beerwall))

								<svg
										class="w-4 h-4"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
								>
									<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M5 13l4 4L19 7"
									/>
								</svg>

								Zaktualizuj piwo

							@else

								<svg
										class="w-4 h-4"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
								>
									<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M12 4v16m8-8H4"
									/>
								</svg>

								+ Dodaj

							@endif

						</button>

					</div>
