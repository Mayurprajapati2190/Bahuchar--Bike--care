export function buildWhatsAppUrl(phone, message) {
    let digits = String(phone ?? '').replace(/\D/g, '');

    if (digits.length === 10) {
        digits = `91${digits}`;
    } else if (digits.startsWith('0') && digits.length === 11) {
        digits = `91${digits.slice(1)}`;
    }

    return `https://wa.me/${digits}?text=${encodeURIComponent(message)}`;
}
