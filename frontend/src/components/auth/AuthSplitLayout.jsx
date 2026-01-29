export default function AuthSplitLayout({ title, subtitle, quote, imageUrl, children }) {
  return (
    <section className="mx-auto w-full max-w-6xl">
      <div className="grid grid-cols-1 overflow-hidden rounded-2xl border border-[#E8B04A]/25 bg-[#FFF8F0] shadow-[0_12px_28px_rgba(51,51,51,0.08)] lg:grid-cols-2">
        <div className="relative min-h-[240px] bg-[#F2E6D8]">
          <img
            src={imageUrl}
            alt="Warm food and lifestyle"
            className="h-full w-full object-cover"
            loading="lazy"
          />
          <div className="absolute inset-0 bg-gradient-to-t from-[#333333]/55 via-[#333333]/25 to-transparent" />
          <div className="absolute bottom-6 left-6 right-6">
            <p className="text-sm font-medium text-[#FFF8F0]/95">{quote}</p>
          </div>
        </div>

        <div className="bg-[#FFF8F0] p-5 sm:p-8 lg:p-10">
          <header className="mb-6 space-y-2">
            <h1 className="text-2xl font-semibold tracking-tight text-[#333333] sm:text-3xl">{title}</h1>
            <p className="text-sm text-[#333333]/75 sm:text-base">{subtitle}</p>
          </header>
          {children}
        </div>
      </div>
    </section>
  );
}
