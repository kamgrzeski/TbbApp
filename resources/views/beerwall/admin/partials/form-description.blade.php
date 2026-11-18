					<div class="mb-8">

						<div class="flex items-center gap-3 mb-5 pb-3 border-b border-gray-100">

							<div class="flex-shrink-0 w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">

								<svg
										class="w-5 h-5 text-purple-600"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
								>
									<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M4 6h16M4 12h16M4 18h10"
									/>
								</svg>

							</div>

							<div>

								<h3 class="font-bold text-gray-800">
									Opis piwa
								</h3>

								<p class="text-xs text-gray-500 mt-0.5">
									Krótki opis widoczny dla klientów.
								</p>

							</div>

						</div>

						<textarea
								name="beer_description"
								rows="4"
								maxlength="1000"
								placeholder="Np. Jasne, mocno chmielone piwo o cytrusowym aromacie..."
								class="w-full border-gray-300 rounded-md shadow-sm
                                   focus:border-blue-500 focus:ring-blue-500
                                   resize-y"
						>{{ old('beer_description', $beerwall->beer_description ?? '') }}</textarea>

					</div>
