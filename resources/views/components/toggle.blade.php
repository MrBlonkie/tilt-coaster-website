@props([
    'id' => 'toggle-' . uniqid(),
    'name' => 'toggle',
    'checked' => false,
])

<div class="toggler flex flex-col items-center gap-1.5">
    <input type="checkbox" id="{{ $id }}" name="{{ $name }}" {{ $checked ? 'checked' : '' }} {{ $attributes }}>
    <label for="{{ $id }}">
        <span class="track-off">OFF</span>
        <span class="track-on">ON</span>
    </label>
    <span class="toggler-name">{{ $slot }}</span>
</div>

<style>
    .toggler {
        width: auto;
    }

    .toggler input {
        display: none;
    }

    .toggler label {
        display: block;
        position: relative;
        width: 84px;
        height: 42px;
        border-radius: 42px;
        background: #374151;
        border: 1.5px solid #4b5563;
        cursor: pointer;
        transition: background 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }

    /* Knob */
    .toggler label::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 4px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: white;
        transform: translateY(-50%);
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.18), 0 0 0 1px rgba(0, 0, 0, 0.05);
        transition: left 0.2s cubic-bezier(0.4, 0, 0.2, 1),
                    box-shadow 0.2s ease;
    }

    /* Active track */
    .toggler input:checked + label {
        background: #1d4ed8;
        border-color: #1d4ed8;
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.18);
    }

    /* Knob slid to right */
    .toggler input:checked + label::after {
        left: calc(100% - 36px);
        box-shadow: 0 1px 6px rgba(29, 78, 216, 0.3), 0 0 0 1px rgba(0, 0, 0, 0.05);
    }

    /* OFF text — visible by default */
    .toggler label .track-off {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        font-family: ui-monospace, 'Courier New', monospace;
        font-size: 9px;
        font-weight: 700;
        color: #9ca3af;
        letter-spacing: 0.08em;
        opacity: 1;
        transition: opacity 0.15s ease;
        pointer-events: none;
        user-select: none;
    }

    /* ON text — hidden by default */
    .toggler label .track-on {
        position: absolute;
        top: 50%;
        left: 10px;
        transform: translateY(-50%);
        font-family: ui-monospace, 'Courier New', monospace;
        font-size: 9px;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.9);
        letter-spacing: 0.08em;
        opacity: 0;
        transition: opacity 0.15s ease;
        pointer-events: none;
        user-select: none;
    }

    .toggler input:checked + label .track-on {
        opacity: 1;
    }

    .toggler input:checked + label .track-off {
        opacity: 0;
    }

    /* Label below */
    .toggler .toggler-name {
        font-family: ui-monospace, 'Courier New', monospace;
        font-size: 10px;
        font-weight: 600;
        color: #6b7280;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        transition: color 0.2s ease;
    }

    .toggler input:checked ~ .toggler-name {
        color: #1d4ed8;
    }
</style>
