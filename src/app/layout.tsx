import type { Metadata, Viewport } from 'next';
import localFont from 'next/font/local';
import { Onest } from 'next/font/google';
import './globals.css';

/* Brand faces, self-hosted so the clone carries no external font dependency. */
const forma = localFont({
  src: [{ path: '../fonts/FormaDJRCyrillicDisplay-Regular-Testing.woff2', weight: '400', style: 'normal' }],
  variable: '--font-forma',
  display: 'swap',
  preload: true,
});

const cofo = localFont({
  src: [
    { path: '../fonts/CoFoSans-Regular.woff2', weight: '400', style: 'normal' },
    { path: '../fonts/CoFoSans-Medium.woff2', weight: '500', style: 'normal' },
    { path: '../fonts/CoFoSans-Bold.woff2', weight: '700', style: 'normal' },
    { path: '../fonts/CoFoSans-Black.woff2', weight: '900', style: 'normal' },
  ],
  variable: '--font-cofo',
  display: 'swap',
  preload: true,
});

const robert = localFont({
  src: [{ path: '../fonts/CoFoRobert-RegularItalic.woff2', weight: '400', style: 'italic' }],
  variable: '--font-robert',
  display: 'swap',
});

const onest = Onest({ subsets: ['latin', 'cyrillic'], variable: '--font-onest', display: 'swap' });

export const metadata: Metadata = {
  title: 'Экосфера — отдых вне города',
  description:
    'Уютные домики, свежий воздух, тишина леса и всё необходимое для идеальных выходных, отпуска или семейного отдыха. 40 минут от Владивостока.',
  openGraph: {
    title: 'Экосфера — отдых вне города',
    description: 'Уютные домики в лесу, 40 минут от Владивостока.',
    locale: 'ru_RU',
    type: 'website',
  },
};

export const viewport: Viewport = {
  width: 'device-width',
  initialScale: 1,
  themeColor: '#123206',
};

/**
 * Sets the artboard scale factors before first paint.
 *
 * The design is a fixed-width canvas, so fidelity comes from scaling rather
 * than reflowing — the same thing the original page does. Running this as a
 * blocking inline script (not an effect) puts the correct scale in place for
 * the very first frame, so nothing jumps on load.
 */
const SCALE_SCRIPT = `(function(){
var d=document.documentElement;
function s(){var w=d.clientWidth;d.style.setProperty('--sd',w/1920);d.style.setProperty('--sm',w/375);}
s();
addEventListener('resize',s,{passive:true});
addEventListener('orientationchange',s);
})();`;

export default function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  return (
    <html
      lang="ru"
      className={`${forma.variable} ${cofo.variable} ${robert.variable} ${onest.variable}`}
    >
      <head>
        <script dangerouslySetInnerHTML={{ __html: SCALE_SCRIPT }} />
      </head>
      <body>{children}</body>
    </html>
  );
}
