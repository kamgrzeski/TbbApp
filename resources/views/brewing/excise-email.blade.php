<x-app-layout>
	
	{{-- ================================================================
		 HEADER
	================================================================= --}}
	
	<x-slot name="header">
		
		<div class="flex justify-between items-center">
			
			<div>
				
				<h2 class="font-bold text-2xl text-gray-800 leading-tight">
					E-maile akcyzowe
				</h2>
				
				<p class="text-sm text-gray-500 mt-1">
					{{ $recipe->name }}
					— {{ $recipe->volume }} litrów
					— zbiornik nr {{ $recipe->tank_number }}
				</p>
			
			</div>
			
			<a
					href="{{ route('brewing.show', $recipe) }}"
					class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition"
			>
				Powrót
			</a>
		
		</div>
	
	</x-slot>
	
	
	{{-- ================================================================
		 CONTENT
	================================================================= --}}
	
	<div class="py-6">
		
		<div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
			
			@foreach($recipe->batches->sortBy('batch_number') as $index => $batch)
				
				@php
					
					/*
					|--------------------------------------------------------------------------
					| NUMER WARKI
					|--------------------------------------------------------------------------
					*/

					$batchNumber = $batch->batch_number;


					/*
					|--------------------------------------------------------------------------
					| NUMER WARKI W RECEPTURZE
					|--------------------------------------------------------------------------
					|
					| 1 = pierwszy zestaw słodów/chmieli
					| 2 = drugi zestaw słodów/chmieli
					|
					*/

					$recipeBatchNumber = $index + 1;


					/*
					|--------------------------------------------------------------------------
					| SŁODY
					|--------------------------------------------------------------------------
					*/

					$batchMalts = $recipe->malts
						->where('batch_number', $recipeBatchNumber);


					/*
					|--------------------------------------------------------------------------
					| CHMIELE
					|--------------------------------------------------------------------------
					*/

					$batchHops = $recipe->hops
						->where('batch_number', $recipeBatchNumber);


					/*
					|--------------------------------------------------------------------------
					| ŁĄCZNA ILOŚĆ SŁODÓW
					|--------------------------------------------------------------------------
					*/

					$totalMalt = $batchMalts->sum('kg');


					/*
					|--------------------------------------------------------------------------
					| DATA WARZENIA
					|--------------------------------------------------------------------------
					*/

					$productionDate = now()
						->addDay()
						->format('d.m.Y');


					/*
					|--------------------------------------------------------------------------
					| TEMAT
					|--------------------------------------------------------------------------
					*/

					$subject =
						'Zgłoszenie planowanego warzenia piwa – Warka nr '
						. $batchNumber;


					/*
					|--------------------------------------------------------------------------
					| ID EDYTORA
					|--------------------------------------------------------------------------
					*/

					$editorId = 'emailContent_' . $batchNumber;

					$subjectId = 'emailSubject_' . $batchNumber;

					$buttonId = 'copyContentButton_' . $batchNumber;

					$messageId = 'copyMessage_' . $batchNumber;


					/*
					|--------------------------------------------------------------------------
					| HTML EMAILA
					|--------------------------------------------------------------------------
					*/

					$emailHtml = '<div style="font-family: Arial, Helvetica, sans-serif; font-size: 13px; line-height: 1.5; color: #222;">';


					/*
					|--------------------------------------------------------------------------
					| POWITANIE
					|--------------------------------------------------------------------------
					*/

					$emailHtml .= '
						<p style="margin: 0 0 14px 0;">
							Szanowni Państwo,
						</p>
					';


					/*
					|--------------------------------------------------------------------------
					| INFORMACJA O WARZENIU
					|--------------------------------------------------------------------------
					*/

					$emailHtml .= '
						<p style="margin: 0 0 14px 0;">
							uprzejmie informuję, że w dniu
							' . e($productionDate) . ' r.
							w zakładzie zlokalizowanym przy
							ul. Staszica 3A/U1, 20-081 Lublin,
							należącym do Twin Brothers Brewery,
							planowane jest warzenie piwa w ilości
							500 litrów.
							Jest to Warka nr ' . e($batchNumber) . '.
						</p>
					';


					/*
					|--------------------------------------------------------------------------
					| SŁODY
					|--------------------------------------------------------------------------
					*/

					$emailHtml .= '
						<p style="margin: 0 0 6px 0;">
							Użyte surowce słodowe:
						</p>

						<ul style="
							margin-top: 0;
							margin-bottom: 14px;
							padding-left: 24px;
						">
					';


					foreach ($batchMalts as $malt) {

						$emailHtml .= '
							<li style="margin-bottom: 3px;">
								' . e($malt->name) . ' –
								' . number_format($malt->kg, 2, ',', ' ') . ' kg
							</li>
						';
					}


					$emailHtml .= '</ul>';


					/*
					|--------------------------------------------------------------------------
					| CHMIELE
					|--------------------------------------------------------------------------
					*/

					$emailHtml .= '
						<p style="margin: 0 0 6px 0;">
							Użyte chmiele:
						</p>

						<ul style="
							margin-top: 0;
							margin-bottom: 14px;
							padding-left: 24px;
						">
					';


					foreach ($batchHops as $hop) {

						$emailHtml .= '
							<li style="margin-bottom: 3px;">
								' . e($hop->name) . ' –
								' . number_format($hop->amount, 2, ',', ' ') . ' g
							</li>
						';
					}


					$emailHtml .= '</ul>';


					/*
					|--------------------------------------------------------------------------
					| ŁĄCZNA ILOŚĆ SŁODÓW
					|--------------------------------------------------------------------------
					*/

					$emailHtml .= '
						<p style="margin: 0 0 14px 0;">
							Łączna ilość surowców słodowych:
							' . number_format($totalMalt, 2, ',', ' ') . ' kg.
						</p>
					';


					/*
					|--------------------------------------------------------------------------
					| GODZINY WARZENIA
					|--------------------------------------------------------------------------
					*/

					$emailHtml .= '
						<p style="margin: 0 0 14px 0;">
							Planowane rozpoczęcie warzenia o godzinie
							16:00, koniec 23:30.
						</p>
					';


					/*
					|--------------------------------------------------------------------------
					| PODSTAWA INFORMACJI
					|--------------------------------------------------------------------------
					*/

					$emailHtml .= '
						<p style="margin: 0;">
							Informacja ta przekazywana jest w związku z obowiązkiem
							powiadamiania Urzędu o terminach produkcji piwa.
						</p>
					';


					$emailHtml .= '</div>';
				
				@endphp
				
				
				{{-- ========================================================
					 KARTA EMAILA
				========================================================= --}}
				
				<div class="bg-white shadow-sm rounded-xl p-6 mb-6">
					
					
					{{-- ====================================================
						 NAGŁÓWEK
					===================================================== --}}
					
					<div class="flex justify-between items-start mb-6">
						
						<div>
							
							<h3 class="font-bold text-xl text-gray-800">
								Warka nr {{ $batchNumber }}
							</h3>
							
							<p class="text-sm text-gray-500 mt-1">
								{{ $recipe->name }}
								— 500 litrów
								— zbiornik nr {{ $recipe->tank_number }}
							</p>
						
						</div>
						
						
						<div>

                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-800 text-xs font-semibold">
                                Warka {{ $recipeBatchNumber }}
                            </span>
						
						</div>
					
					</div>
					
					
					{{-- ====================================================
						 INFORMACJA
					===================================================== --}}
					
					<div class="mb-6 p-4 rounded-lg bg-purple-50 border border-purple-200">
						
						<h4 class="font-semibold text-purple-900">
							E-mail akcyzowy — Warka nr {{ $batchNumber }}
						</h4>
						
						<p class="text-sm text-purple-700 mt-1">
							Wiadomość została wygenerowana dla
							<strong>warki nr {{ $batchNumber }}</strong>.
						</p>
					
					</div>
					
					
					{{-- ====================================================
						 TEMAT
					===================================================== --}}
					
					<div class="mb-6">
						
						<label
								for="{{ $subjectId }}"
								class="block text-sm font-semibold text-gray-700 mb-2"
						>
							Temat wiadomości
						</label>
						
						
						<div class="flex gap-2">
							
							<input
									id="{{ $subjectId }}"
									type="text"
									readonly
									value="{{ $subject }}"
									class="flex-1 rounded-lg border-gray-300 bg-gray-50 shadow-sm text-sm"
							/>
							
							
							<button
									type="button"
									onclick="copySubject('{{ $subjectId }}', this)"
									class="px-4 py-2 bg-gray-700 text-white rounded-lg text-xs font-semibold uppercase tracking-widest hover:bg-gray-800 transition"
							>
								Kopiuj temat
							</button>
						
						</div>
					
					</div>
					
					
					{{-- ====================================================
						 TREŚĆ
					===================================================== --}}
					
					<div class="flex justify-between items-end mb-3">
						
						<div>
							
							<label class="block text-sm font-semibold text-gray-700">
								Treść wiadomości
							</label>
							
							<p class="text-xs text-gray-500 mt-1">
								Możesz poprawić treść przed skopiowaniem.
							</p>
						
						</div>
						
						
						<button
								type="button"
								onclick="copyEmailContent('{{ $editorId }}', '{{ $messageId }}', this)"
								id="{{ $buttonId }}"
								class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg font-semibold text-xs uppercase tracking-widest hover:bg-purple-700 shadow-md transition"
						>
							Kopiuj treść
						</button>
					
					</div>
					
					
					{{-- ====================================================
						 EDYTOR
					===================================================== --}}
					
					<div
							id="{{ $editorId }}"
							contenteditable="true"
							class="border border-gray-300 rounded-lg p-6 bg-white min-h-[400px] focus:outline-none focus:ring-2 focus:ring-purple-500"
							style="white-space: normal;"
					>{!! $emailHtml !!}</div>
					
					
					{{-- ====================================================
						 KOMUNIKAT
					===================================================== --}}
					
					<div
							id="{{ $messageId }}"
							class="hidden mt-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm font-medium"
					>
						✓ Treść została skopiowana. Możesz teraz wkleić ją bezpośrednio do Gmaila lub Outlooka.
					</div>
				
				</div>
			
			@endforeach
		
		
		</div>
	
	</div>
	
	
	{{-- ================================================================
		 JAVASCRIPT
	================================================================= --}}
	
	<script>

        /*
        |--------------------------------------------------------------------------
        | KOPIOWANIE TEMATU
        |--------------------------------------------------------------------------
        */

        async function copySubject(inputId, button) {

            const subjectInput =
                document.getElementById(inputId);

            const subject =
                subjectInput.value.trim();


            try {

                await navigator.clipboard.writeText(subject);

            } catch (error) {

                subjectInput.select();

                document.execCommand('copy');

                subjectInput.blur();

            }


            const originalText =
                button.innerText;


            button.innerText =
                '✓ Skopiowano';


            setTimeout(() => {

                button.innerText =
                    originalText;

            }, 2000);

        }


        /*
        |--------------------------------------------------------------------------
        | KOPIOWANIE TREŚCI EMAILA
        |--------------------------------------------------------------------------
        */

        async function copyEmailContent(
            editorId,
            messageId,
            button
        ) {

            const content =
                document.getElementById(editorId);

            const message =
                document.getElementById(messageId);


            /*
            |--------------------------------------------------------------------------
            | KOPIA ZAWARTOŚCI
            |--------------------------------------------------------------------------
            */

            const clone =
                content.cloneNode(true);


            /*
            |--------------------------------------------------------------------------
            | USUWAMY PUSTE TEKSTOWE WĘZŁY
            |--------------------------------------------------------------------------
            */

            while (
                clone.firstChild &&
                clone.firstChild.nodeType === Node.TEXT_NODE &&
                !clone.firstChild.textContent.trim()
                ) {

                clone.removeChild(
                    clone.firstChild
                );

            }


            /*
            |--------------------------------------------------------------------------
            | USUWAMY WHITESPACE PRZED PIERWSZYM ELEMENTEM
            |--------------------------------------------------------------------------
            */

            const firstElement =
                clone.firstElementChild;


            if (firstElement) {

                while (
                    firstElement.firstChild &&
                    firstElement.firstChild.nodeType === Node.TEXT_NODE &&
                    !firstElement.firstChild.textContent.trim()
                    ) {

                    firstElement.removeChild(
                        firstElement.firstChild
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | HTML
            |--------------------------------------------------------------------------
            */

            const html =
                clone.innerHTML.trim();


            /*
            |--------------------------------------------------------------------------
            | TEXT
            |--------------------------------------------------------------------------
            */

            const tempDiv =
                document.createElement('div');


            tempDiv.innerHTML =
                html;


            const text =
                tempDiv.innerText.trim();


            try {

                /*
                |--------------------------------------------------------------------------
                | NOWOCZESNE KOPIOWANIE HTML + TEXT
                |--------------------------------------------------------------------------
                */

                if (
                    navigator.clipboard &&
                    window.ClipboardItem
                ) {

                    const clipboardItem =
                        new ClipboardItem({

                            'text/html': new Blob(
                                [html],
                                {
                                    type: 'text/html'
                                }
                            ),

                            'text/plain': new Blob(
                                [text],
                                {
                                    type: 'text/plain'
                                }
                            )

                        });


                    await navigator.clipboard.write([
                        clipboardItem
                    ]);

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | FALLBACK
                    |--------------------------------------------------------------------------
                    */

                    const range =
                        document.createRange();


                    range.selectNodeContents(
                        content
                    );


                    const selection =
                        window.getSelection();


                    selection.removeAllRanges();

                    selection.addRange(
                        range
                    );


                    document.execCommand(
                        'copy'
                    );


                    selection.removeAllRanges();

                }


                /*
                |--------------------------------------------------------------------------
                | SUKCES
                |--------------------------------------------------------------------------
                */

                const originalText =
                    button.innerText;


                button.innerText =
                    '✓ Skopiowano';


                message.classList.remove(
                    'hidden'
                );


                setTimeout(() => {

                    button.innerText =
                        originalText;

                    message.classList.add(
                        'hidden'
                    );

                }, 3000);


            } catch (error) {

                console.error(
                    'Błąd podczas kopiowania:',
                    error
                );


                alert(
                    'Nie udało się automatycznie skopiować treści. Zaznacz treść ręcznie i użyj Ctrl+C.'
                );

            }

        }
	
	</script>

</x-app-layout>
