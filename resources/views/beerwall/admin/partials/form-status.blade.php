					<div class="mb-8">

						<div class="flex items-center gap-3 mb-5 pb-3 border-b border-gray-100">

							<div class="flex-shrink-0 w-9 h-9 rounded-lg bg-gray-100 flex items-center justify-center">

								<svg
										class="w-5 h-5 text-gray-600"
										fill="none"
										stroke="currentColor"
										viewBox="0 0 24 24"
								>
									<path
											stroke-linecap="round"
											stroke-linejoin="round"
											stroke-width="2"
											d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
									/>
								</svg>

							</div>

							<div>

								<h3 class="font-bold text-gray-800">
									Status piwa
								</h3>

								<p class="text-xs text-gray-500 mt-0.5">
									Wybierz jeden status dla piwa.
								</p>

							</div>

						</div>

						<div class="grid grid-cols-1 md:grid-cols-4 gap-4">

							<label class="relative flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer
                                          transition hover:bg-gray-50
                                          {{ $currentStatus === 'normal'
                                                ? 'border-blue-500 bg-blue-50'
                                                : 'border-gray-200 bg-white' }}">

								<input
										type="radio"
										name="beer_status"
										value="normal"
										class="w-5 h-5 border-gray-300 text-blue-600 focus:ring-blue-500"
										{{ $currentStatus === 'normal' ? 'checked' : '' }}
								>

								<div>

									<div class="font-bold text-sm text-gray-800">
										NORMALNE
									</div>

									<div class="text-xs text-gray-500 mt-0.5">
										Standardowa oferta
									</div>

								</div>

							</label>

							<label class="relative flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer
                                          transition hover:bg-red-50
                                          {{ $currentStatus === 'premiere'
                                                ? 'border-red-500 bg-red-50'
                                                : 'border-gray-200 bg-white' }}">

								<input
										type="radio"
										name="beer_status"
										value="premiere"
										class="w-5 h-5 border-gray-300 text-red-600 focus:ring-red-500"
										{{ $currentStatus === 'premiere' ? 'checked' : '' }}
								>

								<div>

									<div class="font-bold text-sm text-red-700">
										PREMIERA
									</div>

									<div class="text-xs text-gray-500 mt-0.5">
										Nowe piwo
									</div>

								</div>

							</label>


							<label class="relative flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer
                                          transition hover:bg-amber-50
                                          {{ $currentStatus === 'coming_soon'
                                                ? 'border-amber-500 bg-amber-50'
                                                : 'border-gray-200 bg-white' }}">

								<input
										type="radio"
										name="beer_status"
										value="coming_soon"
										class="w-5 h-5 border-gray-300 text-amber-500 focus:ring-amber-500"
										{{ $currentStatus === 'coming_soon' ? 'checked' : '' }}
								>

								<div>

									<div class="font-bold text-sm text-amber-600">
										WKRÓTCE
									</div>

									<div class="text-xs text-gray-500 mt-0.5">
										Jeszcze niedostępne
									</div>

								</div>

							</label>

							<label class="relative flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer
                                          transition hover:bg-gray-50
                                          {{ $currentStatus === 'ended'
                                                ? 'border-gray-500 bg-gray-100'
                                                : 'border-gray-200 bg-white' }}">

								<input
										type="radio"
										name="beer_status"
										value="ended"
										class="w-5 h-5 border-gray-300 text-gray-700 focus:ring-gray-500"
										{{ $currentStatus === 'ended' ? 'checked' : '' }}
								>

								<div>

									<div class="font-bold text-sm text-gray-700">
										WYPRZEDANE
									</div>

									<div class="text-xs text-gray-500 mt-0.5">
										Piwo niedostępne
									</div>

								</div>

							</label>

							<label class="relative flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer
              transition hover:bg-zinc-50
              {{ $currentStatus === 'is_draft'
                    ? 'border-zinc-500 bg-zinc-100'
                    : 'border-gray-200 bg-white' }}">

								<input
										type="radio"
										name="beer_status"
										value="is_draft"
										class="w-5 h-5 border-gray-300 text-zinc-700 focus:ring-zinc-500"
										{{ $currentStatus === 'is_draft' ? 'checked' : '' }}
								>

								<div>
									<div class="font-bold text-sm text-zinc-700">
										SZKIC
									</div>

									<div class="text-xs text-gray-500 mt-0.5">
										Wersja robocza, niewidoczna na kranie
									</div>
								</div>

							</label>

						</div>

					</div>
