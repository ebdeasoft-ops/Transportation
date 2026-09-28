<style>
    /* ============ ورقة بوليصة الشحن (نفس شكل الدفتر) ============ */
    .wb-paper {
        --wb-ink: #2b2f8f;
        --wb-line: #7a7fc4;
        background: #fff;
        color: var(--wb-ink);
        max-width: 900px;
        margin: 0 auto;
        padding: 26px 30px 18px;
        border: 1px solid #dfe2f0;
        border-radius: 6px;
        box-shadow: 0 2px 12px rgba(20, 30, 90, .07);
        direction: rtl;
        font-family: 'Cairo', Tahoma, sans-serif;
    }
    .wb-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; }
    .wb-head-side { width: 27%; font-weight: 700; font-size: 14px; }
    .wb-head-left { text-align: left; }
    .wb-head-center { flex: 1; text-align: center; }
    .wb-logo { max-height: 70px; max-width: 170px; object-fit: contain; margin-bottom: 4px; }
    .wb-name-ar { font-size: 25px; font-weight: 800; line-height: 1.3; }
    .wb-name-en { font-size: 15px; font-weight: 600; letter-spacing: .3px; }
    .wb-cr { font-size: 14px; font-weight: 700; margin-top: 4px; display: flex; align-items: center; gap: 8px; justify-content: center; }
    .wb-rule { display: inline-block; flex: 1; height: 3px; background: var(--wb-ink); border-radius: 2px; min-width: 40px; }
    .wb-date-line { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
    .wb-date-val { flex: 1; text-align: center; border-bottom: 1px dotted var(--wb-line); min-height: 22px; font-family: Tahoma, sans-serif; direction: ltr; }
    .wb-mob { margin-top: 8px; font-size: 13px; }
    .wb-no-box { display: inline-block; border: 2px solid var(--wb-ink); padding: 4px 14px; font-size: 20px; font-weight: 800; font-family: Arial, sans-serif; }
    .wb-no { color: #c0392b; font-weight: 500; }
    .wb-date-input input { width: 100%; border: none; background: #f5f6ff; border-radius: 6px; color: #111; font-weight: 700; font-size: 14px; text-align: center; padding: 3px 4px; outline: none; font-family: Tahoma, sans-serif; cursor: pointer; }
    .wb-date-input input:focus { box-shadow: 0 0 0 2px var(--wb-ink); }
    .wb-no-auto { color: #c0392b; font-weight: 500; }
    .wb-no-hint { display: block; font-size: 11px; color: #8a8fb8; font-weight: 600; margin-top: 3px; font-family: 'Cairo', sans-serif; }
    .wb-no input { width: 110px; border: none; border-bottom: 1px dashed #c0392b; color: #c0392b; font-size: 20px; text-align: center; background: transparent; outline: none; }
    .wb-title-wrap { text-align: center; margin: 8px 0 18px; }
    .wb-title { font-size: 28px; font-weight: 800; border-bottom: 3px double var(--wb-ink); padding: 0 18px 2px; }

    /* السطور المنقطة */
    .wb-row { display: flex; gap: 22px; margin-bottom: 10px; align-items: flex-end; }
    .wb-field { display: flex; align-items: flex-end; gap: 6px; flex: 1; min-width: 0; }
    .wb-field.wide { flex: 2.2; }
    .wb-field > label { white-space: nowrap; font-weight: 800; font-size: 15px; margin: 0; }
    .wb-field .wb-in, .wb-field .wb-val {
        flex: 1; min-width: 0; border: none; border-bottom: 1.5px dotted var(--wb-line); background: transparent;
        color: #111; font-weight: 600; font-size: 15px; padding: 2px 6px; outline: none; min-height: 28px;
    }
    .wb-field .wb-in:focus { border-bottom: 1.5px solid var(--wb-ink); background: #f5f6ff; }
    .wb-suffix { font-weight: 800; font-size: 15px; white-space: nowrap; }

    .wb-picker { display: flex; gap: 6px; flex: 1; min-width: 0; align-items: center; }
    .wb-picker .select2-container { flex: 1; min-width: 0 !important; }
    .wb-picker select { flex: 1; min-width: 0; }
    .wb-add { flex: none; width: 30px; height: 30px; border-radius: 50%; border: 1.5px solid var(--wb-ink); background: #fff; color: var(--wb-ink); font-weight: 900; font-size: 18px; line-height: 1; cursor: pointer; }
    .wb-add:hover { background: var(--wb-ink); color: #fff; }

    /* الجدول */
    .wb-table-wrap { border: 3px double var(--wb-ink); padding: 3px; margin: 18px 0 14px; }
    table.wb-table { width: 100%; border-collapse: collapse; }
    table.wb-table th, table.wb-table td { border: 1.5px solid var(--wb-ink); text-align: center; }
    table.wb-table th { font-weight: 800; font-size: 15px; padding: 5px; background: #fff; color: var(--wb-ink); }
    table.wb-table td { padding: 0; height: 34px; border-top: 1px dotted var(--wb-line); border-bottom: 1px dotted var(--wb-line); }
    table.wb-table td input { width: 100%; border: none; background: transparent; text-align: center; font-weight: 600; font-size: 14px; padding: 6px 4px; outline: none; color: #111; }
    table.wb-table td input:focus { background: #f5f6ff; }
    table.wb-table td.wb-idx { width: 42px; font-weight: 700; }
    table.wb-table tfoot td { font-weight: 800; border-top: 1.5px solid var(--wb-ink); }
    .wb-del-row { color: #c0392b; cursor: pointer; font-weight: 900; border: none; background: none; }

    .wb-signs { display: flex; justify-content: space-between; margin: 10px 0 14px; font-weight: 800; font-size: 15px; }
    .wb-note-strong { font-weight: 800; font-size: 16px; margin: 6px 0 10px; }
    .wb-footer { border-top: 2px solid var(--wb-ink); margin-top: 18px; padding-top: 8px; text-align: center; font-weight: 700; font-size: 13px; }
    .wb-footer div { margin-bottom: 2px; }

    .wb-actions { max-width: 900px; margin: 16px auto 30px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
    .wb-actions .btn { min-width: 150px; font-weight: 700; border-radius: 8px; }

    @media screen and (max-width: 767px) {
        .wb-paper { padding: 14px 12px; }
        .wb-head { flex-direction: column; align-items: stretch; }
        .wb-head-side { width: 100%; text-align: right !important; }
        .wb-row { flex-direction: column; gap: 10px; }
        .wb-field > label { font-size: 14px; }
        .wb-table-wrap { overflow-x: auto; }
        table.wb-table { min-width: 640px; }
    }
</style>
