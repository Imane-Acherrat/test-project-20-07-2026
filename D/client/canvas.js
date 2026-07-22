export class CanvasRenderer {
    constructor(canvasElement) {
        this.canvas = canvasElement;
        this.ctx = this.canvas.getContext('2d');
        this.objects = new Map();
        this.cursors = new Map();
        this.selectedObjectId = null;
    }

    setObjects(objectsArray) {
        this.objects.clear();
        if (Array.isArray(objectsArray)) {
            objectsArray.forEach((obj) => this.objects.set(obj.id, obj));
        }
        this.render();
    }

    addOrUpdateObject(object) {
        if (!object || !object.id) return;
        this.objects.set(object.id, object);
        this.render();
    }

    deleteObject(id) {
        this.objects.delete(id);
        if (this.selectedObjectId === id) {
            this.selectedObjectId = null;
        }
        this.render();
    }

    clear() {
        this.objects.clear();
        this.selectedObjectId = null;
        this.render();
    }

    updateCursor(userId, cursorData) {
        this.cursors.set(userId, cursorData);
        this.render();
    }

    removeCursor(userId) {
        this.cursors.delete(userId);
        this.render();
    }

    findObjectAt(x, y) {
        const objs = Array.from(this.objects.values()).reverse();
        return objs.find((obj) => {
            if (obj.type === 'rectangle') {
                const minX = Math.min(obj.x, obj.x + (obj.width || 0));
                const maxX = Math.max(obj.x, obj.x + (obj.width || 0));
                const minY = Math.min(obj.y, obj.y + (obj.height || 0));
                const maxY = Math.max(obj.y, obj.y + (obj.height || 0));
                return x >= minX && x <= maxX && y >= minY && y <= maxY;
            }
            if (obj.type === 'circle') {
                const dist = Math.hypot(x - obj.x, y - obj.y);
                return dist <= (obj.radius || 0);
            }
            if (obj.type === 'text') {
                return x >= obj.x && x <= obj.x + 100 && y >= obj.y - 20 && y <= obj.y;
            }
            return false;
        });
    }

    render() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);

        for (const obj of this.objects.values()) {
            this.drawObject(obj);
        }

        for (const [id, cursor] of this.cursors.entries()) {
            this.drawCursor(cursor);
        }
    }

    drawObject(obj) {
        this.ctx.save();
        this.ctx.strokeStyle = obj.strokeColor || '#000000';
        this.ctx.fillStyle = obj.fillColor || 'transparent';
        this.ctx.lineWidth = obj.strokeWidth || 2;

        if (this.selectedObjectId === obj.id) {
            this.ctx.shadowColor = '#2563eb';
            this.ctx.shadowBlur = 8;
        }

        this.ctx.beginPath();

        if (obj.type === 'rectangle') {
            this.ctx.rect(obj.x, obj.y, obj.width || 0, obj.height || 0);
            if (obj.fillColor && obj.fillColor !== 'transparent') {
                this.ctx.fillRect(obj.x, obj.y, obj.width || 0, obj.height || 0);
            }
            this.ctx.stroke();
        } else if (obj.type === 'circle') {
            this.ctx.arc(obj.x, obj.y, Math.max(0, obj.radius || 0), 0, Math.PI * 2);
            if (obj.fillColor && obj.fillColor !== 'transparent') {
                this.ctx.fill();
            }
            this.ctx.stroke();
        } else if (obj.type === 'line') {
            this.ctx.moveTo(obj.x1 || obj.x, obj.y1 || obj.y);
            this.ctx.lineTo(obj.x2 || obj.x, obj.y2 || obj.y);
            this.ctx.stroke();
        } else if (obj.type === 'text') {
            this.ctx.font = `${obj.fontSize || 20}px sans-serif`;
            this.ctx.fillStyle = obj.fillColor || obj.strokeColor || '#000000';
            this.ctx.fillText(obj.text || '', obj.x, obj.y);
        }

        this.ctx.restore();
    }

    drawCursor(cursor) {
        this.ctx.save();
        this.ctx.fillStyle = '#ef4444';
        this.ctx.beginPath();
        this.ctx.arc(cursor.x, cursor.y, 5, 0, Math.PI * 2);
        this.ctx.fill();

        if (cursor.username) {
            this.ctx.font = '12px sans-serif';
            this.ctx.fillStyle = '#374151';
            this.ctx.fillText(cursor.username, cursor.x + 8, cursor.y + 4);
        }
        this.ctx.restore();
    }
}