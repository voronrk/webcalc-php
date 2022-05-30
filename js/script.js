'use strict';

const sizeField = document.querySelector('#size');
const pageQuantityField = document.querySelector('#page-quantity');
const quantityField = document.querySelector('#quantity');
const defaultInkField = document.querySelector('#default-ink');
const rollWidthField = document.querySelector('#roll-width');
const btnRun = document.querySelector('button');
const pagesInkSelect = document.querySelector('#pages-ink');

// const resultFormsQuantity = document.querySelector('#result-forms-quantity');
// const resultRollsQuantity = document.querySelector('#result-rolls-quantity');
// const resultLayoutInks = document.querySelector('#result-layout-inks');
// const resultInkMap = document.querySelector('#result-ink-map');
const result = document.querySelector('#result');


let pageQuantity = 0;
let inkOnPages = [];
let pages = [];
pageQuantityField.value = '';

class Cell {
    pageNum;
    ink = [];

    colors = [
        '',
        'is-clickable',
        'is-clickable has-background-primary',
        'is-clickable has-background-warning',
        'is-clickable has-background-info',
    ]

    update(value) {
        this.ink = value;
        this.view.className = this.colors[this.ink.length];
        this.view.querySelector('#ink').innerText = this.ink.length;
    }

    render() {
        let cell = document.createElement('td');
        cell.innerHTML = `
            <span class="is-size-7">${this.pageNum}</span><br>
            <span class="has-text-weight-bold" id="ink"></span>
        `;
        cell.addEventListener('click', ()=> {
            switch (this.ink.length) {
                case 1:
                    this.update([1,2,3,4]);
                    break;
                case 2:
                    this.update([1]);
                    break;
                case 4:
                    this.update([1,2]);
                    break;
            }
        });
        return cell;
    }

    constructor(pageNum, inks) {
        this.pageNum = pageNum;
        for (let i=1;i<=inks;i++) {
            this.ink.push(i);
        }
        this.view = this.render();
        this.update(this.ink);
    }
}

function renderPageInkSelect() {
    pagesInkSelect.innerHTML = '';
    pages = [];
    for(let item=1; item<=pageQuantity; item++) {
        let cell = new Cell(item, +defaultInkField.value);
        pages.push(cell);
        pagesInkSelect.appendChild(cell.view);
    };    
}

function renderResult_DEPRECATED(data) {
    resultFormsQuantity.innerHTML = '';
    resultRollsQuantity.innerHTML = '';
    resultInkMap.innerHTML = `
    <tr>
        <th>Спуск</th>
        <th>Красочность</th>
    </tr>
    `;
    resultFormsQuantity.innerHTML = data.formQuantity;
    resultRollsQuantity.innerHTML = data.rollsQuantity;
    resultLayoutInks.innerHTML = data.layoutInkMap;

    for(let index in data.inkMap) {
        resultInkMap.innerHTML += `
        <tr>
            <td>${index}</td>
            <td>${data.inkMap[index].length}</td>
        </tr>
        `;
    }

}

function renderResult(data) {
    result.innerHTML = `
    <tr>
        <th>Параметр</th>
        <th>Значение</th>
    </tr>
    `;
    for(let index in data) {
        result.innerHTML += `
        <tr>
            <td>${data[index].title}</td>
            <td>${data[index].value}</td>
        </tr>
        `;
    }

}

async function calculate(pageQuantity, size, inks, quantity, rollWidth) {
    const data={'pageQuantity': pageQuantity, 'size': size, 'inks': inks, 'quantity': quantity, 'rollWidth': rollWidth};
    const response = await fetch ('calculate.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    });
    return await response.json();
};

btnRun.addEventListener('click', ()=> {
    inkOnPages = [];
    for (let page of pages) {
        inkOnPages.push(page.ink);
    };
    calculate(pageQuantityField.value, sizeField.value, inkOnPages, quantityField.value, rollWidthField.value)
        .then((data) => {
            renderResult(data);
        });
});

pageQuantityField.addEventListener('input', ()=> {
    pageQuantity = pageQuantityField.value;
    renderPageInkSelect();
});
