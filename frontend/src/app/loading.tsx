export default function Loading() {
  return (
    <div
      className="fixed inset-0 z-[1000] flex items-center justify-center bg-gradient-to-br from-[#FFE8DF] via-white to-[#FFF7ED]"
      aria-live="polite"
      aria-busy="true"
    >
      {/* Preloader animation (from preloader-animation) */}
      <div className="loader">
        <span></span>
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  );
}