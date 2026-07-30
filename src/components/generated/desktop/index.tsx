import { DBackdrop } from './DBackdrop';
import { DS01Block } from './DS01Block';
import { DS02UezzhatDaleko } from './DS02UezzhatDaleko';
import { DS03KrasnyyDom } from './DS03KrasnyyDom';
import { DS04KorichnevyyDom } from './DS04KorichnevyyDom';
import { DS05VklyuchenoVStoimost } from './DS05VklyuchenoVStoimost';
import { DS06ZhivyeFoto } from './DS06ZhivyeFoto';
import { DS07ChemEkosferaOtlichaetsya } from './DS07ChemEkosferaOtlichaetsya';
import { DS08PodhoditDlya } from './DS08PodhoditDlya';
import { DS09Zabronirovat } from './DS09Zabronirovat';
import { DS10ChtoGovoryatTe } from './DS10ChtoGovoryatTe';
import { DS11Chasto } from './DS11Chasto';
import { DS12MinutOtVladivostoka } from './DS12MinutOtVladivostoka';

/** desktop artboard — 1920×12699, 12 sections. */
export function DArtboard() {
  return (
    <>
      <DBackdrop />
      <DS01Block />
      <DS02UezzhatDaleko />
      <DS03KrasnyyDom />
      <DS04KorichnevyyDom />
      <DS05VklyuchenoVStoimost />
      <DS06ZhivyeFoto />
      <DS07ChemEkosferaOtlichaetsya />
      <DS08PodhoditDlya />
      <DS09Zabronirovat />
      <DS10ChtoGovoryatTe />
      <DS11Chasto />
      <DS12MinutOtVladivostoka />
    </>
  );
}
