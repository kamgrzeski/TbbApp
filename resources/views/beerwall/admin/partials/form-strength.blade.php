					<div class="mb-8">

						<div class="flex items-center gap-3 mb-5 pb-3 border-b border-gray-100">

							<div class="flex-shrink-0 w-9 h-9 rounded-lg bg-amber-50 flex items-center justify-center">

								<svg
										class="w-5 h-5 text-amber-600"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
								>
									<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M12 3v18m9-9H3"
									/>
								</svg>

							</div>

							<div>

								<h3 class="font-bold text-gray-800">
									Parametry piwa
								</h3>

								<p class="text-xs text-gray-500 mt-0.5">
									Podstawowe parametry techniczne.
								</p>

							</div>

						</div>

						<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

							<div>

								<label
										for="beer_blg"
										class="block text-sm font-bold text-gray-700 mb-1.5"
								>
									Ekstrakt (Blg)
								</label>

								<div class="relative">

									<input
											id="beer_blg"
											type="text"
											name="beer_blg"
											value="{{ old('beer_blg', $beerwall->beer_blg ?? '') }}"
											placeholder="np. 14"
											class="w-full border-gray-300 rounded-md shadow-sm
                                               focus:border-blue-500 focus:ring-blue-500
                                               pr-14"
									>
								</div>

							</div>

							<div>

								<label
										for="beer_alc"
										class="block text-sm font-bold text-gray-700 mb-1.5"
								>
									Alkohol (%)
								</label>

								<div class="relative">

									<input
											id="beer_alc"
											type="text"
											name="beer_alc"
											value="{{ old('beer_alc', $beerwall->beer_alc ?? '') }}"
											placeholder="np. 5.6"
											class="w-full border-gray-300 rounded-md shadow-sm
                                               focus:border-blue-500 focus:ring-blue-500
                                               pr-10"
									>

								</div>

							</div>

						</div>

					</div>
