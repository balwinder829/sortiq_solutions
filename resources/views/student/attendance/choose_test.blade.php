@extends('layouts.public')

@section('content')
<div class="container test-page-container">
    <div class="test-page-card mx-auto">

        <div class="test-page-header">
            <div class="header-icon">✓</div>
            <h2>Choose Your Test</h2>
            <p>Select the assessment type you want to attempt.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        @php
            $codingOptions = $options->where('test_type', 'technical');
            $nonCodingOptions = $options->where('test_type', 'non_technical');
            $nonCodingCategories = $nonCodingOptions->groupBy('category_name');
        @endphp

        <div class="row g-3">

            {{-- CODING --}}
            <div class="col-12 col-md-6">
                @if ($codingOptions->isNotEmpty())
                    @php $codingOption = $codingOptions->first(); @endphp

                    <form method="POST"
                          action="{{ route('form.choose.submit') }}"
                          class="h-100">
                        @csrf

                        <input type="hidden"
                               name="option_id"
                               value="{{ $codingOption->id }}">

                        <button type="submit" class="choice-card coding-card">
                            <span class="choice-symbol coding-symbol">&lt;/&gt;</span>

                            <span class="choice-title">Coding</span>

                            <span class="choice-description">
                                Technical assessment
                            </span>

                            <span class="choice-action">
                                Start Coding Test <span aria-hidden="true">→</span>
                            </span>
                        </button>
                    </form>
                @else
                    <div class="choice-card unavailable-card">
                        <span class="choice-symbol coding-symbol">&lt;/&gt;</span>
                        <span class="choice-title">Coding</span>
                        <span class="choice-description">
                            Technical assessment
                        </span>
                        <span class="unavailable-text">
                            Coding test is not available.
                        </span>
                    </div>
                @endif
            </div>

            {{-- NON-CODING --}}
            <div class="col-12 col-md-6">
                <button type="button"
                        id="nonCodingToggle"
                        class="choice-card noncoding-card"
                        aria-expanded="false"
                        aria-controls="nonCodingCategories">

                    <span class="choice-symbol noncoding-symbol">☷</span>

                    <span class="choice-title">Non-Coding</span>

                    <span class="choice-description">
                        HR, Finance, Digital Marketing and more
                    </span>

                    <span class="choice-action">
                        Choose Non-Coding <span aria-hidden="true">→</span>
                    </span>
                </button>
            </div>
        </div>

        {{-- CATEGORIES: HIDDEN UNTIL NON-CODING IS SELECTED --}}
        <div id="nonCodingCategories" class="category-panel" hidden>
            <div class="category-panel-header">
                <h5>Select a Category</h5>
                <p>Choose one category to continue.</p>
            </div>

            @if ($nonCodingCategories->isNotEmpty())
                <form method="POST"
                      action="{{ route('form.choose.submit') }}"
                      id="nonCodingForm">
                    @csrf

                    <div class="category-list">
                        @foreach ($nonCodingCategories as $category => $categoryOptions)
                            @php
                                $option = $categoryOptions->first();
                                $categoryLabel = $category ?: 'Other';
                            @endphp

                            <label class="category-option">
                                <input type="radio"
                                       name="option_id"
                                       value="{{ $option->id }}"
                                       required>

                                <span>{{ $categoryLabel }}</span>
                                <span class="category-check">✓</span>
                            </label>
                        @endforeach
                    </div>

                    <button type="submit"
                            id="continueButton"
                            class="btn btn-primary continue-button"
                            disabled>
                        Continue to Selected Test →
                    </button>
                </form>
            @else
                <div class="alert alert-warning mb-0">
                    Non-Coding tests are not available.
                </div>
            @endif
        </div>

    </div>
</div>

<style>
    .test-page-container {
        max-width: 1000px;
        padding-top: 35px;
        padding-bottom: 45px;
    }

    .test-page-card {
        max-width: 850px;
        padding: 32px;
        border: 1px solid #e5eaf1;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 8px 30px rgba(20, 35, 60, .06);
    }

    .test-page-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .header-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        margin: 0 auto 15px;
        border-radius: 14px;
        background: #edf4ff;
        color: #1769e0;
        font-size: 25px;
        font-weight: 700;
    }

    .test-page-header h2 {
        margin-bottom: 8px;
        color: #172033;
        font-size: 29px;
        font-weight: 750;
    }

    .test-page-header p {
        margin-bottom: 0;
        color: #697586;
        font-size: 15px;
    }

    .choice-card {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
        min-height: 220px;
        height: 100%;
        padding: 23px;
        border: 1px solid #e0e6ef;
        border-radius: 15px;
        background: #fff;
        text-align: left;
        color: #172033;
        cursor: pointer;
        transition: border-color .2s ease, box-shadow .2s ease,
                    transform .2s ease;
    }

    .choice-card:hover {
        border-color: #8ab4f8;
        box-shadow: 0 8px 22px rgba(32, 90, 170, .09);
        transform: translateY(-2px);
    }

    .choice-card:focus-visible {
        outline: 3px solid rgba(13, 110, 253, .25);
        outline-offset: 3px;
    }

    .choice-symbol {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        margin-bottom: 18px;
        border-radius: 13px;
        font-size: 19px;
        font-weight: 700;
    }

    .coding-symbol {
        background: #edf4ff;
        color: #1769e0;
    }

    .noncoding-symbol {
        background: #eaf8f0;
        color: #16834a;
        font-size: 27px;
    }

    .choice-title {
        display: block;
        margin-bottom: 7px;
        font-size: 21px;
        font-weight: 700;
    }

    .choice-description {
        display: block;
        color: #697586;
        font-size: 14px;
        line-height: 1.5;
    }

    .choice-action {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        margin-top: auto;
        padding-top: 22px;
        color: #1769e0;
        font-size: 14px;
        font-weight: 700;
    }

    .noncoding-card .choice-action {
        color: #16834a;
    }

    .unavailable-card {
        cursor: not-allowed;
        opacity: .7;
    }

    .unavailable-text {
        margin-top: 20px;
        color: #b42318;
        font-size: 13px;
    }

    .category-panel {
        margin-top: 25px;
        padding: 22px;
        border: 1px solid #e0e6ef;
        border-radius: 15px;
        background: #f8fafc;
    }

    .category-panel-header h5 {
        margin-bottom: 5px;
        font-weight: 700;
        color: #172033;
    }

    .category-panel-header p {
        margin-bottom: 17px;
        color: #697586;
        font-size: 14px;
    }

    .category-option {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 58px;
        margin-bottom: 10px;
        padding: 14px 16px;
        border: 1px solid #dfe5ed;
        border-radius: 10px;
        background: #fff;
        cursor: pointer;
        transition: border-color .2s ease, background .2s ease;
    }

    .category-option:hover {
        border-color: #8ab4f8;
    }

    .category-option:has(input:checked) {
        border-color: #1769e0;
        background: #eff6ff;
    }

    .category-option input {
        width: 18px;
        height: 18px;
        margin: 0;
        flex-shrink: 0;
        accent-color: #1769e0;
    }

    .category-option > span:first-of-type {
        flex: 1;
        color: #263246;
        font-size: 15px;
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .category-check {
        display: none;
        color: #1769e0;
        font-weight: 700;
    }

    .category-option:has(input:checked) .category-check {
        display: inline;
    }

    .continue-button {
        width: 100%;
        margin-top: 8px;
        padding: 13px;
        border-radius: 9px;
        font-weight: 600;
    }

    .continue-button:disabled {
        cursor: not-allowed;
        opacity: .55;
    }

    @media (max-width: 767.98px) {
        .test-page-container {
            padding-top: 20px;
            padding-bottom: 25px;
        }

        .test-page-card {
            padding: 22px 17px;
            border-radius: 14px;
        }

        .test-page-header {
            margin-bottom: 23px;
        }

        .test-page-header h2 {
            font-size: 24px;
        }

        .choice-card {
            min-height: 190px;
            padding: 20px;
        }

        .category-panel {
            padding: 16px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .choice-card,
        .category-option {
            transition: none;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('nonCodingToggle');
    const categoryPanel = document.getElementById('nonCodingCategories');
    const form = document.getElementById('nonCodingForm');
    const continueButton = document.getElementById('continueButton');

    if (toggle && categoryPanel) {
        toggle.addEventListener('click', function () {
            const shouldOpen = categoryPanel.hidden;

            categoryPanel.hidden = !shouldOpen;
            toggle.setAttribute('aria-expanded', String(shouldOpen));

            if (shouldOpen) {
                categoryPanel.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }
        });
    }

    if (form && continueButton) {
        form.querySelectorAll('input[name="option_id"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                continueButton.disabled = false;
            });
        });
    }
});
</script>
@endsection