					<div class="mb-8">

						<div class="flex items-center gap-3 mb-5 pb-3 border-b border-gray-100">

							<div class="flex-shrink-0 w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">

								<svg
										class="w-5 h-5 text-green-600"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
								>
									<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M12 8c-1.657 0-3 .895-3 2s1.343 3 3 3 3 .895 3 2-1.343 2-3 2m0-10v2m0 8v2m9-6a9 9 0 11-18 0 9 9 0 0118 0z"
									/>
								</svg>

							</div>

							<div>
								<h3 class="font-bold text-gray-800">
									Ceny
								</h3>

								<p class="text-xs text-gray-400 mt-0.5">
									Ceny sprzedaży piwa.
								</p>
							</div>

						</div>

						<div class="rounded-xl border border-gray-200 overflow-hidden bg-white">

							<div class="flex items-center justify-between px-5 py-2.5 bg-gray-50 border-b border-gray-200">

        <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">
            Pojemność
        </span>

								<span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">
            Cena (zł)
        </span>

							</div>

							<div class="flex items-center justify-between gap-6 px-5 py-4 border-b border-gray-100">

								<div class="flex items-center gap-3">

									<div class="w-10 h-10 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center">

                <span class="text-sm font-bold text-gray-600">
                    0.25
                </span>

									</div>

									<div>
										<div class="text-sm font-semibold text-gray-800">
											0.25 L
										</div>

										<div class="text-[11px] text-gray-400">
											mała porcja
										</div>
									</div>

								</div>

								<div class="relative w-36">

									<input
											id="beer_price_small"
											type="number"
											step="0.01"
											min="0"
											name="beer_price_small"
											value="{{ old('beer_price_small', $beerwall->beer_price_small ?? '') }}"
											placeholder="0.00"
											class="w-full h-10
                       border-gray-200
                       bg-gray-50
                       rounded-lg
                       pr-9
                       text-right
                       font-semibold
                       text-gray-800
                       focus:bg-white
                       focus:border-green-500
                       focus:ring-green-500"
											required
									>

								</div>

							</div>

							<div class="flex items-center justify-between gap-6 px-5 py-4 border-b border-gray-100">

								<div class="flex items-center gap-3">

									<div class="w-10 h-10 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-center">

                <span class="text-sm font-bold text-blue-600">
                    0.5
                </span>

									</div>

									<div>
										<div class="text-sm font-semibold text-gray-800">
											0.5 L
										</div>

										<div class="text-[11px] text-gray-400">
											standardowa porcja
										</div>
									</div>

								</div>

								<div class="relative w-36">

									<input
											id="beer_price_medium"
											type="number"
											step="0.01"
											min="0"
											name="beer_price_medium"
											value="{{ old('beer_price_medium', $beerwall->beer_price_medium ?? '') }}"
											placeholder="0.00"
											class="w-full h-10
                       border-gray-200
                       bg-gray-50
                       rounded-lg
                       pr-9
                       text-right
                       font-semibold
                       text-gray-800
                       focus:bg-white
                       focus:border-green-500
                       focus:ring-green-500"
											required
									>

								</div>

							</div>

							<div class="flex items-center justify-between gap-6 px-5 py-4">

								<div class="flex items-center gap-3">

									<div class="w-10 h-10 rounded-lg bg-gray-50 border border-gray-200 flex items-center justify-center">

                <span class="text-sm font-bold text-gray-600">
                    1.0
                </span>

									</div>

									<div>
										<div class="text-sm font-semibold text-gray-800">
											1.0 L
										</div>

										<div class="text-[11px] text-gray-400">
											duża porcja
										</div>
									</div>

								</div>

								<div class="relative w-36">

									<input
											id="beer_price_large"
											type="number"
											step="0.01"
											min="0"
											name="beer_price_large"
											value="{{ old('beer_price_large', $beerwall->beer_price_large ?? '') }}"
											placeholder="0.00"
											class="w-full h-10
                       border-gray-200
                       bg-gray-50
                       rounded-lg
                       pr-9
                       text-right
                       font-semibold
                       text-gray-800
                       focus:bg-white
                       focus:border-green-500
                       focus:ring-green-500"
											required
									>

								</div>

							</div>

						</div>
					</div>
