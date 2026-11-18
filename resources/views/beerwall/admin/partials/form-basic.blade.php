					<div class="mb-8">

						<div class="flex items-center gap-3 mb-5 pb-3 border-b border-gray-100">

							<div class="flex-shrink-0 w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">

								<svg
										class="w-5 h-5 text-blue-600"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
								>
									<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M13 16h-1v-4h-1m1-4h.01M12 3a9 9 0 110 18 9 9 0 010-18z"
									/>
								</svg>

							</div>

							<div>

								<h3 class="font-bold text-gray-800">
									Informacje o piwie
								</h3>

								<p class="text-xs text-gray-500 mt-0.5">
									Podstawowe informacje wyświetlane na Beer Wall.
								</p>

							</div>

						</div>

						<div class="grid grid-cols-1 md:grid-cols-3 gap-5">

							<div class="md:col-span-2">

								<label
										for="beer_name"
										class="block text-sm font-bold text-gray-700 mb-1.5"
								>
									Nazwa piwa
									<span class="text-red-500">*</span>
								</label>

								<input
										id="beer_name"
										type="text"
										name="beer_name"
										value="{{ old('beer_name', $beerwall->beer_name ?? '') }}"
										placeholder="np. American IPA"
										class="w-full border-gray-300 rounded-md shadow-sm
                                           focus:border-blue-500 focus:ring-blue-500"
										required
								>

								@error('beer_name')
								<p class="mt-1 text-xs text-red-600">
									{{ $message }}
								</p>
								@enderror

							</div>

							<div>

								<label
										for="position"
										class="block text-sm font-bold text-gray-700 mb-1.5"
								>
									Pozycja / kran
								</label>

								<input
										id="position"
										type="text"
										name="position"
										value="{{ old('position', $beerwall->position ?? '') }}"
										placeholder="np. 3"
										class="w-full border-gray-300 rounded-md shadow-sm
                                           focus:border-blue-500 focus:ring-blue-500"
								>

								<p class="mt-1 text-xs text-gray-400">
									Numer lub oznaczenie kranu.
								</p>

							</div>

							<div class="md:col-span-3">

								<label
										for="beer_style"
										class="block text-sm font-bold text-gray-700 mb-1.5"
								>
									Styl piwa
								</label>

								<input
										id="beer_style"
										type="text"
										name="beer_style"
										value="{{ old('beer_style', $beerwall->beer_style ?? '') }}"
										placeholder="np. IPA, Pils, Porter, Sour..."
										class="w-full border-gray-300 rounded-md shadow-sm
                                           focus:border-blue-500 focus:ring-blue-500"
								>

							</div>

						</div>

					</div>
