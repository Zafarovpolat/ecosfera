import { MBackdrop } from './MBackdrop';
import { MS01Block } from './MS01Block';
import { MS02UezzhatDaleko } from './MS02UezzhatDaleko';
import { MS03KrasnyyDom } from './MS03KrasnyyDom';
import { MS04Block } from './MS04Block';
import { MS05ZhivyeFoto } from './MS05ZhivyeFoto';
import { MS06ChemEkosferaOtlichaetsya } from './MS06ChemEkosferaOtlichaetsya';
import { MS07PodhoditDlya } from './MS07PodhoditDlya';
import { MS08Zabronirovat } from './MS08Zabronirovat';
import { MS09ChtoGovoryatTe } from './MS09ChtoGovoryatTe';
import { MS10Chasto } from './MS10Chasto';
import { MS11MinutOtVladivostoka } from './MS11MinutOtVladivostoka';

/** mobile artboard — 375×11520, 11 sections. */
export function MArtboard() {
  return (
    <>
      <MBackdrop />
      <MS01Block />
      <MS02UezzhatDaleko />
      <MS03KrasnyyDom />
      <MS04Block />
      <MS05ZhivyeFoto />
      <MS06ChemEkosferaOtlichaetsya />
      <MS07PodhoditDlya />
      <MS08Zabronirovat />
      <MS09ChtoGovoryatTe />
      <MS10Chasto />
      <MS11MinutOtVladivostoka />
    </>
  );
}
