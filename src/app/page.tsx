import { DArtboard } from '@/components/generated/desktop';
import { MArtboard } from '@/components/generated/mobile';

/**
 * Экосфера — landing page.
 *
 * Two artboards, each a 1:1 reconstruction of its Figma frame (1920 desktop /
 * 375 mobile). Only one is mounted visually at a time; the swap happens at
 * 768px, and each canvas scales proportionally to the viewport.
 */
export default function Home() {
  return (
    <main>
      <div className="canvas canvas--desktop only-desktop">
        <div className="canvas__inner">
          <DArtboard />
        </div>
      </div>
      <div className="canvas canvas--mobile only-mobile">
        <div className="canvas__inner">
          <MArtboard />
        </div>
      </div>
    </main>
  );
}
